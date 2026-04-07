# ChessCoachAI
ChessCoachAI is a web application designed to help chess players analyze their games and improve their skills. By providing a PGN (Portable Game Notation) of a chess game and specifying whether you played as white or black, the application offers detailed insights into your gameplay. It highlights your strengths, identifies areas for improvement, and provides actionable advice to help you become a better chess player.

<img width="927" height="978" alt="image" src="https://github.com/user-attachments/assets/07601a28-b4a8-42bc-bcdb-fad8c455a25f" />

## Tech Stack
### Frontend
Vue.js: The frontend is built using Vue.js, a progressive JavaScript framework for building user interfaces. It provides a seamless and interactive user experience.
### Backend
Laravel (PHP): The backend is powered by Laravel, a robust PHP framework. Laravel handles routing, business logic, and API endpoints for the application.  
Illuminate: Illuminate is used to transfer the data between the Vue client and PHP backend.
### Caching/Storage
Redis: Redis is used as a caching layer to improve the performance of the application. It stores frequently accessed data, reducing the need for repetitive computations.

## Future Considerations
- Adding a login, so data could be stored per user
- Persistant database storage. MongoDB or some other NoSQL database would probably be selected, due to the flexibility of storing the data associated to each user and the ability to add new functionality easily. This system could learn over time, not just base your strengths/weaknesses based off one game alone.
- Retries when calls to Gemini fail. Sometimes errors pop up, such as if the Gemini server is experiencing heavy usage.

## How It Works
Input: The user uploads a PGN file of their chess game and specifies whether they played as white or black.  
Analysis: The backend processes the PGN and analyzes the game. It evaluates the moves, identifies key moments, and assesses the overall gameplay.  
Insights: The application generates a detailed report with:
  - Pros: What you did well during the game.
  - Cons: Areas where you can improve.
  - Tips: Helpful advice to enhance your chess skills.
  - Output: The insights are displayed on the frontend, providing a clear and actionable summary of the analysis.

## Getting Started

### Prerequisites
- PHP 8.4
- Composer
- Node.js
- Redis

### Navigate to base directory
- Navigate to ChessCoachAI folder if not already there
`cd ChessCoachAI`

### Install dependencies:
`composer install`
`npm install`

### Run Redis Server
On Windows: `redis-server`
On Mac: `brew services start redis`

### Set up the environment:
- Copy .env.example to .env
`cp .env.example .env`
- Generate App Key
Run `php artisan key:generate`
- Input Gemini API Key (retrieve from https://aistudio.google.com/api-keys)
Update GEMINI_API_KEY in .env file

### Run the frontend app:
- Run frontend app
`npm run dev`

### Start the development server:
- Run server
`php artisan serve`

### Usage
Open the application in your browser at http://localhost:8000.
Upload a PGN file and specify your side (white or black).
View the analysis and insights provided by the application.

