# 🌐 Wshop API
A lightweight and modern Symfony-based API for managing stores, providing filtering, sorting, and a strong foundation for future e-commerce features.

---

## 🧩 Features

- **Symfony 7** backend architecture
- **FrankenPHP** for high-performance PHP applications
- **Dockerized environment** for consistent development
- **Doctrine ORM + Migrations**
- **Nelmio API Documentation (OpenAPI 3)**
- **Fixtures & Factories** powered by Foundry
- **Automated tests** (Unit + Integration)
- **Makefile tooling** for a smoother workflow

---

## 🚀 Getting Started

### 1. Start the development environment

```bash
make start
```

### 2. Install and trust the local HTTPS certificate

```bash
make trust-cert
```

### 3. Initialize the test database (create DB + run migrations)

```bash
make init-db-test
```

### 4. Load fixtures into the development database

```bash
make fixtures
```

### 5. Access the API documentation

➡️ **https://localhost/api/doc**  
The API documentation is powered by NelmioApiDocBundle and includes an interactive Swagger UI.
You can browse all endpoints, inspect request & response schemas, and execute API calls directly from the browser.

---

## 🧪 Running Tests

The project includes a complete test setup using PHPUnit and Foundry.

```bash
make test
```

---

## 🛠 Useful Make Commands

| Command            | Description                                   |
|--------------------|-----------------------------------------------|
| `make start`       | Build & start all containers                  |
| `make trust-cert`  | Trust local HTTPS CA (once per machine)       |
| `make init-db-test`| Initialize the test database                  |
| `make fixtures`    | Load development fixtures                     |
| `make test`        | Run the full test suite                       |
| `make sh`          | Open a shell inside the PHP container         |
| `make sf c=about`  | Run Symfony commands                          |

![PHP](https://img.shields.io/badge/PHP-8.4-777bb4)
![CI](https://github.com/AlexandreCavanna/wshop-api/actions/workflows/ci.yaml/badge.svg)
![PHPStan](https://img.shields.io/badge/PHPStan-Level%20Max-brightgreen)
![Rector](https://img.shields.io/badge/Rector-enabled-blueviolet)
![Docker](https://img.shields.io/badge/Docker-🐳_Ready-blue)
