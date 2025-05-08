# 💰 Interest Account Library 🐘

![PHPUnit](https://img.shields.io/github/actions/workflow/status/gil-ss/interest-account-lib-php/tests.yml?branch=main&label=PHPUnit&logo=php&logoColor=white)
[![Cobertura de código](https://codecov.io/gh/gil-ss/interest-account-lib-php/branch/main/graph/badge.svg)](https://codecov.io/gh/gil-ss/interest-account-lib-php)


![PHP](https://img.shields.io/badge/PHP-7A86B8?logo=php&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-7A86B8?logo=docker&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-7A86B8?logo=composer&logoColor=white)
![Makefile](https://img.shields.io/badge/Makefile-7A86B8?logo=gnu&logoColor=white)

## Overview
The **Interest Account Library** is a PHP-based system for managing interest-bearing accounts. It allows users to open accounts, deposit funds, calculate interest, and retrieve account statements. The library integrates with an external API to determine interest rates based on user income.

## Features
- Open an interest-bearing account
- Deposit funds
- Automatically calculate interest
- Retrieve account statements
- Store data using different storage methods (e.g., JSON file, in-memory storage)
- Integration with an external **Stats API** to determine user income
- Fully tested with PHPUnit
- Automated interest calculation using a scheduled cron job
- Uses **Money pattern** for precise financial calculations
- Implements **interface-based architecture** for flexibility

## Installation
### Prerequisites
- PHP 8.2+
- Composer
- Docker (optional, for containerized execution)

### Steps
1. Clone the repository:
   ```sh
   git clone https://github.com/your-repo/interest-account-library.git
   cd interest-account-library
   ```
2. Install dependencies:
   ```sh
   composer install
   ```
3. Run tests:
   ```sh
   make test
   ```
4. Generate code coverage report:
   ```sh
   make coverage
   ```

## Usage
### Open an Account
```php
$storage = new JsonStorage('accounts.json');
$statsApi = new StatsApiClient(new Client(), 'https://stats.dev.test/');
$service = new InterestAccountService($storage, $statsApi);

$service->openAccount('user-123');
```

### Deposit Funds
```php
$service->deposit('user-123', new Money(50000)); // 500.00 in cents
```

### Calculate Interest
```php
$service->calculateInterest('user-123');
```

### Retrieve Account Statement
```php
$transactions = $service->getAccountStatement('user-123');
print_r($transactions);
```

## Running with Docker
To run the application in a Docker container:
```sh
make up
```
To stop the container:
```sh
make down
```

## Setting Up Cron Job for Interest Calculation
To ensure interest calculations run automatically every 3 days, add the following entry to your system's crontab:

```sh
0 0 */3 * * php /path/to/interest-account-library/src/Scripts/calculate-interest.php >> /var/log/interest_account.log 2>&1
```

- Replace `/path/to/interest-account-library/` with the actual path where the project is stored.
- This ensures the interest calculation script runs every 3 days at midnight and logs output to `/var/log/interest_account.log`.

To edit your crontab:
```sh
crontab -e
```

To verify scheduled jobs:
```sh
crontab -l
```

## Code Quality & Testing
- **Uses PHPUnit** for unit testing.
- **Mockery** for testing service dependencies.
- **Interfaces for abstraction** allow easy replacement of components.
- **Money pattern ensures precision in calculations.**

---
---

<p align="center"><strong>Developed with ❤️ by Gil</strong></p>

