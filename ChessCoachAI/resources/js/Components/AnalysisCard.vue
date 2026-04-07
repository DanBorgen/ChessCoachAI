<script setup>
/**
 * AnalysisCard.vue
 * Receives the parsed Gemini JSON as `analysis` prop and renders it.
 * This is a "presentational" component — no logic, just display.
 */
defineProps({
  analysis: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <div class="card">
    <div class="card-header">
      <div class="opening-name">{{ analysis.opening?.name }}</div>
      <div class="opening-eval">{{ analysis.opening?.evaluation }}</div>
    </div>

    <p class="summary">{{ analysis.game_summary }}</p>

    <div class="two-col">
      <div class="section section--pro">
        <div class="section-title"><span class="dot" />Positives</div>
        <div v-for="(pro, i) in analysis.pros" :key="i" class="list-item">{{ pro }}</div>
      </div>
      <div class="section section--con">
        <div class="section-title"><span class="dot" />Concerns</div>
        <div v-for="(con, i) in analysis.cons" :key="i" class="list-item">{{ con }}</div>
      </div>
    </div>

    <div v-for="(s, i) in analysis.strengths" :key="'s' + i" class="insight insight--str">
      <div class="insight-title">
        {{ s.title }}
        <span class="tag tag--str">Strength</span>
      </div>
      <div class="insight-desc">{{ s.description }}</div>
    </div>

    <div v-for="(w, i) in analysis.weaknesses" :key="'w' + i" class="insight insight--wk">
      <div class="insight-title">
        {{ w.title }}
        <span class="tag tag--wk">Improve</span>
      </div>
      <div class="insight-desc">{{ w.description }}</div>
    </div>

    <div v-if="analysis.key_moments?.length" class="key-moments">
      <div class="km-label">Key Moments</div>
      <div v-for="(km, i) in analysis.key_moments" :key="i" class="km-item">
        <span class="km-move">{{ km.move }}</span>
        <span class="km-sig">{{ km.significance }}</span>
      </div>
    </div>

    <div v-if="analysis.improvement_tip" class="tip">
      <span class="tip-icon">💡</span>
      <div class="tip-text">
        <strong>Study tip:</strong> {{ analysis.improvement_tip }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); /* Added subtle depth for light mode */
}

/* Opening */
.card-header {
  padding: 1rem 1.4rem;
  border-bottom: 1px solid var(--border);
  background: rgba(0,0,0,0.01); /* Very slight tint for header area */
}
.opening-name {
  font-family: 'Playfair Display', serif;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--accent2);
}
.opening-eval {
  font-size: .75rem;
  color: var(--muted);
  margin-top: .15rem;
}

/* Summary */
.summary {
  padding: 1.2rem 1.4rem;
  font-size: .85rem;
  line-height: 1.7;
  color: var(--muted); /* Changed from hardcoded hex */
  font-style: italic;
  border-bottom: 1px solid var(--border);
}

/* Pros/Cons */
.two-col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  border-bottom: 1px solid var(--border);
}
@media (max-width: 560px) {
  .two-col { grid-template-columns: 1fr; }
}

.section { padding: 1.1rem 1.4rem; }
.section--pro { border-right: 1px solid var(--border); }
@media (max-width: 560px) {
  .section--pro { border-right: none; border-bottom: 1px solid var(--border); }
}

.section-title {
  font-size: .65rem;
  letter-spacing: .14em;
  text-transform: uppercase;
  margin-bottom: .85rem;
  display: flex;
  align-items: center;
  gap: .45rem;
  font-weight: 700;
}
.section--pro .section-title { color: var(--pro); }
.section--con .section-title { color: var(--con); }

.dot {
  display: block;
  width: 8px; height: 8px;
  border-radius: 50%; /* Circular dots look cleaner in light mode */
  flex-shrink: 0;
}
.section--pro .dot { background: var(--pro); }
.section--con .dot { background: var(--con); }

.list-item {
  font-size: .8rem;
  line-height: 1.6;
  color: var(--text); /* Darker text for readability */
  padding-left: 1.1rem;
  position: relative;
  margin-bottom: .6rem;
}
.list-item::before { content: '•'; position: absolute; left: 0; color: var(--muted); }

/* Insight cards */
.insight {
  padding: 1.2rem 1.4rem;
  border-bottom: 1px solid var(--border);
}
.insight-title {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: .4rem;
  display: flex;
  align-items: center;
  gap: .6rem;
}
.tag {
  font-family: 'IBM Plex Mono', monospace;
  font-size: .6rem;
  font-weight: 600;
  letter-spacing: .05em;
  text-transform: uppercase;
  padding: .2rem .5rem;
  border-radius: 4px;
}
.tag--str { background: #e3f2fd; color: var(--str); } /* Light blue bg */
.tag--wk  { background: #fbe9e7; color: var(--wk); }  /* Light orange bg */

.insight-desc { 
  font-size: .8rem; 
  line-height: 1.7; 
  color: var(--muted); 
}

/* Key moments */
.key-moments {
  padding: 1.2rem 1.4rem;
  border-bottom: 1px solid var(--border);
  background: rgba(0,0,0,0.01);
}
.km-label {
  font-size: .65rem;
  letter-spacing: .14em;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: .85rem;
  font-weight: 700;
}
.km-item {
  display: flex;
  gap: .8rem;
  margin-bottom: .7rem;
  font-size: .8rem;
  line-height: 1.5;
  align-items: center;
}
.km-move {
  background: var(--bg);
  border: 1px solid var(--border);
  color: var(--accent);
  padding: .2rem .5rem;
  border-radius: 4px;
  white-space: nowrap;
  font-size: .75rem;
  font-weight: 600;
  font-family: 'IBM Plex Mono', monospace;
}
.km-sig { color: var(--text); }

/* Study tip */
.tip {
  padding: 1.2rem 1.4rem;
  background: #fff9eb; /* Soft gold tint */
  display: flex;
  gap: .85rem;
  align-items: flex-start;
}
.tip-icon { font-size: 1.1rem; }
.tip-text {
  font-size: .8rem;
  line-height: 1.7;
  color: var(--text);
}
.tip-text strong { color: var(--accent2); font-weight: 700; }
</style>