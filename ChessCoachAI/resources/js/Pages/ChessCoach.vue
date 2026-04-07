<script setup>
/**
 * ChessCoach.vue — the main Inertia page.
 *
 * Vue 3 Composition API with <script setup> syntax.
 * useForm() from Inertia handles the POST to Laravel and CSRF automatically.
 */
import { ref } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import AnalysisCard from '../Components/AnalysisCard.vue'

// ── State ──────────────────────────────────────────────────────────────────

/** useForm gives us form.pgn, form.post(), form.processing, form.errors */
const form = useForm({ pgn: '', player: 'White' })

/** Array of chat turns: { type: 'user'|'analysis'|'error', payload } */
const messages = ref([])

const SAMPLE_PGN = `[Event "Casual Game"]
[White "Player1"]
[Black "Player2"]
[Result "1-0"]

1. e4 e5 2. Nf3 Nc6 3. Bb5 a6 4. Ba4 Nf6 5. O-O Be7 6. Re1 b5 7. Bb3 d6
8. c3 O-O 9. h3 Nb8 10. d4 Nbd7 11. Nbd2 Bb7 12. Bc2 Re8 13. Nf1 Bf8
14. Ng3 g6 15. b3 Bg7 16. d5 c6 17. dxc6 Bxc6 18. Nxe5 dxe5 19. Qxd8 Raxd8
20. Rxe5 Nd6 21. Bg5 Nf8 22. Rd1 Nfe4 23. Bxe4 Nxe4 24. Rxe8 Rxe8
25. Bxd8 Re1+ 26. Rxe1 1-0`

// ── Methods ────────────────────────────────────────────────────────────────

function loadSample() {
  form.pgn = SAMPLE_PGN
}

function submit() {
  if (!form.pgn.trim() || form.processing) return

  // Push the user's PGN into the chat log
  messages.value.push({ type: 'user', payload: form.pgn })

  /**
   * form.post() sends a POST to /analyze.
   * Inertia normally does full page transitions — using axios directly here
   * lets us stay in the chat without a page reload.
   */
  // form.post('/analyze', {
  //   preserveState: true,
  //   preserveScroll: true,
  //   onSuccess: (page) => {
  //     // Inertia page props carry the response when using Inertia::render with props.
  //     // For a pure JSON endpoint we use onSuccess differently — see note in README.
  //   },
  // })

  // Instead, call the endpoint with fetch for a chat-style response
  const pgn = form.pgn
  form.pgn = ''

  fetchAnalysis(pgn)
}

async function fetchAnalysis(pgn) {
  // Mark processing manually since we're using fetch not form.post()
  form.processing = true

  try {
    const res = await fetch('/analyze', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
          ?? getCsrfFromCookie(),
        'X-Inertia': 'false', // Tell Laravel this is a plain JSON request
        'Accept': 'application/json',
      },
      body: JSON.stringify({ "pgn": pgn, "player": form.player }),
    })

    const data = await res.json()

    if (!res.ok) {
      const msg = data.message ?? data.errors?.pgn?.[0] ?? `Server error (${res.status})`
      messages.value.push({ type: 'error', payload: msg })
    } else {
      messages.value.push({ type: 'analysis', payload: data })
    }
  } catch (err) {
    messages.value.push({ type: 'error', payload: 'Network error: ' + err.message })
  } finally {
    form.processing = false
    scrollToBottom()
  }
}

function getCsrfFromCookie() {
  const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
  return match ? decodeURIComponent(match[1]) : ''
}

function handleKeydown(e) {
  if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
    e.preventDefault()
    submit()
  }
}

function scrollToBottom() {
  setTimeout(() => window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }), 50)
}
</script>

<template>
  <Head title="Chess Coach AI" />

  <!-- Header -->
  <header class="site-header">
    <div class="header-inner">
      <div class="logo-board" aria-hidden="true">
        <span v-for="i in 16" :key="i" />
      </div>
      <div>
        <h1>Chess Coach AI</h1>
        <p>Laravel · Vue · Redis · Gemini</p>
      </div>
    </div>
  </header>

  <main class="main">
    <!-- Chat log -->
    <div class="chat-log">

      <!-- Welcome state -->
      <div v-if="messages.length === 0" class="welcome">
        <div class="king-icon">♚</div>
        <h2>Paste a game, get a coach.</h2>
        <p>Submit any PGN and receive a full breakdown — openings, tactics, endgame, positional play, your top strengths and areas to improve.</p>
        <button class="sample-btn" @click="loadSample">Load a sample PGN →</button>
      </div>

      <!-- Message list -->
      <template v-for="(msg, i) in messages" :key="i">

        <!-- User message -->
        <div v-if="msg.type === 'user'" class="msg msg--user">
          <div class="msg-avatar">♙</div>
          <div class="msg-body">
            <div class="msg-label">Your PGN</div>
            <pre class="pgn-preview">{{ msg.payload }}</pre>
          </div>
        </div>

        <!-- Analysis card -->
        <div v-else-if="msg.type === 'analysis'" class="msg msg--bot">
          <div class="msg-avatar">♚</div>
          <div class="msg-body">
            <AnalysisCard :analysis="msg.payload" />
          </div>
        </div>

        <!-- Error -->
        <div v-else-if="msg.type === 'error'" class="msg msg--bot">
          <div class="msg-avatar">♚</div>
          <div class="msg-body">
            <div class="error-box">⚠ {{ msg.payload }}</div>
          </div>
        </div>

      </template>

      <!-- Thinking indicator -->
      <div v-if="form.processing" class="msg msg--bot">
        <div class="msg-avatar">♚</div>
        <div class="msg-body">
          <div class="thinking">
            Analyzing your game
            <div class="dots">
              <span /><span /><span />
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Input area -->
    <div class="input-area">
      <div class="input-wrap">
        <div>
        <label>Playing As:</label>
        <input type="radio" value="White" name="player" v-model="form.player" style="margin-left: 10px;"> White
        <input type="radio" value="Black" name="player" v-model="form.player" style="margin-left: 10px;"> Black
      </div>
        <textarea
          v-model="form.pgn"
          placeholder="Paste your PGN here…  e.g.  1. e4 e5 2. Nf3 Nc6 ..."
          rows="2"
          @keydown="handleKeydown"
        />
        <button
          class="send-btn"
          :disabled="form.processing || !form.pgn.trim()"
          @click="submit"
        >
          {{ form.processing ? 'Analyzing…' : 'Analyze ♟' }}
        </button>
      </div>
      <p class="input-hint">Paste full PGN including headers, or just the move list. Ctrl+Enter to send.</p>
    </div>
  </main>
</template>

<style scoped>
/* ── Layout ── */
.site-header {
  position: sticky;
  top: 0;
  z-index: 10;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  padding: 1.2rem 2rem;
}
.header-inner {
  max-width: 860px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 1.2rem;
}

.logo-board {
  display: grid;
  grid-template-columns: repeat(4, 14px);
  grid-template-rows: repeat(4, 14px);
  flex-shrink: 0;
}
.logo-board span:nth-child(odd)  { background: var(--accent); }
.logo-board span:nth-child(even) { background: var(--border); }

h1 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(1.2rem, 3vw, 1.8rem);
  font-weight: 900;
  color: var(--accent2);
  line-height: 1;
}
.site-header p {
  font-size: .62rem;
  color: var(--muted);
  letter-spacing: .12em;
  text-transform: uppercase;
  margin-top: .25rem;
}

.main {
  max-width: 860px;
  margin: 0 auto;
  padding: 0 1.5rem;
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 70px);
}

.chat-log {
  flex: 1;
  padding: 2rem 0 1rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* ── Welcome ── */
.welcome {
  margin: auto;
  text-align: center;
  padding: 4rem 1rem;
}
.king-icon { font-size: 3rem; margin-bottom: 1rem; }
.welcome h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.6rem;
  color: var(--accent2);
  margin-bottom: .5rem;
}
.welcome p {
  font-size: .78rem;
  color: var(--muted);
  line-height: 1.8;
  max-width: 400px;
  margin: 0 auto;
}
.sample-btn {
  margin-top: 1.2rem;
  padding: .5rem 1rem;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  font-family: inherit;
  font-size: .72rem;
  color: var(--muted);
  cursor: pointer;
  background: transparent;
  transition: all .2s;
}
.sample-btn:hover { border-color: var(--accent); color: var(--accent); }

/* ── Messages ── */
.msg {
  display: flex;
  gap: 1rem;
  animation: fadeSlide .3s ease;
}
@keyframes fadeSlide {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.msg-avatar {
  width: 32px;
  height: 32px;
  flex-shrink: 0;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}
.msg--user .msg-avatar { background: var(--border); }
.msg--bot  .msg-avatar { background: rgba(201,169,110,.12); border: 1px solid rgba(201,169,110,.25); }

.msg-body { flex: 1; min-width: 0; }
.msg-label { font-size: .62rem; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; }

.pgn-preview {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: .8rem 1rem;
  font-size: .72rem;
  color: var(--muted);
  white-space: pre-wrap;
  word-break: break-all;
  max-height: 110px;
  overflow-y: auto;
}

.error-box {
  background: rgba(192,80,74,.08);
  border: 1px solid rgba(192,80,74,.3);
  border-radius: var(--radius);
  padding: .9rem 1.1rem;
  font-size: .78rem;
  color: #e07070;
}

/* ── Thinking ── */
.thinking {
  display: flex;
  align-items: center;
  gap: .6rem;
  font-size: .75rem;
  color: var(--muted);
}
.dots span {
  display: inline-block;
  width: 5px; height: 5px;
  border-radius: 50%;
  background: var(--accent);
  margin: 0 2px;
  animation: bounce 1.2s infinite;
}
.dots span:nth-child(2) { animation-delay: .2s; }
.dots span:nth-child(3) { animation-delay: .4s; }
@keyframes bounce {
  0%, 80%, 100% { transform: translateY(0); opacity:.4; }
  40%            { transform: translateY(-4px); opacity: 1; }
}

/* ── Input area ── */
.input-area {
  position: sticky;
  bottom: 0;
  background: var(--bg);
  border-top: 1px solid var(--border);
  padding: 1.2rem 0 1.5rem;
}
.input-wrap { display: grid; grid-template-columns: auto; gap: .8rem; align-items: flex-end; }

textarea {
  flex: 1;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  color: var(--text);
  font-family: 'IBM Plex Mono', monospace;
  font-size: .78rem;
  padding: .85rem 1rem;
  resize: none;
  min-height: 56px;
  max-height: 200px;
  outline: none;
  transition: border-color .2s;
  line-height: 1.5;
  field-sizing: content;
}
textarea::placeholder { color: var(--muted); }
textarea:focus { border-color: rgba(201,169,110,.4); }

.send-btn {
  background: var(--accent);
  border: none;
  color: #fff;
  font-family: 'IBM Plex Mono', monospace;
  font-size: .75rem;
  font-weight: 500;
  letter-spacing: .05em;
  padding: 0 1.4rem;
  height: 56px;
  border-radius: var(--radius);
  cursor: pointer;
  transition: background .2s, transform .1s;
  white-space: nowrap;
}
.send-btn:hover:not(:disabled) { background: var(--accent2); }
.send-btn:active:not(:disabled) { transform: scale(.97); }
.send-btn:disabled { opacity: .4; cursor: not-allowed; }

.input-hint { font-size: .65rem; color: var(--muted); margin-top: .4rem; }
</style>
