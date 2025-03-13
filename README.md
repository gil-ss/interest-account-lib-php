# Interest Account Library

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
$statsApi = new StatsApiClient(new Client(), 'https://stats.dev.chip.test/');
$service = new InterestAccountService($storage, $statsApi);

$service->openAccount('user-123');
```

### Deposit Funds
```php
$service->deposit('user-123', 500);
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
0 0 */3 * * php /path/to/interest-account-library/calculate-interest.php >> /var/log/interest_account.log 2>&1
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

---
Developed with ❤️ by Gilberto.

