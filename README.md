# Zonneplan casus

Task: [Casus Software Engineer (PHP/TypeScript)](https://zonneplan.notion.site/Casus-Software-Engineer-PHP-TypeScript-1762101fa2b080dca7bbf83f8a16f139)

## Setup

### Frontend

1. cd into the frontend folder

```bash
cd frontend
```

2. Install dependencies

```bash
pnpm install
```

3. add environment variables

```bash
cp .env.example .env.local
```

4. start the development server

```bash
pnpm dev
```

### Backend

1. cd into the backend folder

```bash
cd backend
```

2. Install dependencies

```bash
composer install
npm install
```

3. add environment variables

```bash
cp .env.example .env
```

The `.env` requires the following variables:

```sh
ZONNEPLAN_API_BASE_URL=""
ZONNEPLAN_API_KEY=""
```

4. start the development server

```bash
composer dev
```

## Tech Stack

### Frontend

- React + Next.js
- Tailwind CSS
- TypeScript
- TanStack Query
- Shadcn UI

Tooling:

- ESLint (linting)
- Prettier (formatting)
- Tsc (typechecking)

### Backend

- PHP
- Laravel

Tooling:

- PHPStan (typechecking)
- Pint (linting)
- Rector (refactoring)
- Pest (testing)

---

## TODO

### Frontend

- add tests

### Backend

- add tests
