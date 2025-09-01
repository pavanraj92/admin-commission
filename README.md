# Commission Package

This package provides commission management for Laravel admin panels. It supports global and category-based commissions, percentage or fixed values, and soft delete functionality.

## Features
- CRUD for commissions
- Commission types: global, category
- Commission calculation: percentage, fixed
- Soft delete support
- Admin panel integration
- AJAX-powered dynamic form fields

## Requirements

- PHP >=8.2
- Laravel Framework >= 12.x

---

## Installation

### 1. Add Git Repository to `composer.json`

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/pavanraj92/admin-commission.git"
    }
]
```

### 2. Require the package via Composer
    ```bash
    composer require admin/commissions:@dev
    ```

### 3. Publish assets
    ```bash
    php artisan commissions:publish --force
    ```
---

## Usage
1. Install the package via Composer:
    ```bash
    composer require admin/commissions:@dev
    ```
2. Run migrations:
    ```bash
    php artisan migrate
    ```
3. Access commission management in the admin panel sidebar.

## Admin Panel Routes

| Method | Endpoint                    | Description             |
|--------|-----------------------------|-------------------------|
| GET    | `/commissions`              | List all commissions    |
| POST   | `/commissions`              | Create a new commission |
| GET    | `/commissions/{id}`         | Get commission details  |
| PUT    | `/commissions/{id}`         | Update a commission     |
| DELETE | `/commissions/{id}`         | Delete a commission     |
| GET    | `/commissions/fetch-options`| To fetch options        |
| POST   | `/commissions/updateStatus` | To update status        |

## AJAX Dynamic Fields
- Changing the commission type in the form fetches categories dynamically.

## Soft Delete
- Deleted commissions are not removed from the database, but marked as deleted.

## Customization
- Views can be published and customized.
- Extend controller logic as needed.

## License
MIT
