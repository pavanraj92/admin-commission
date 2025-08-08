# Commission Package

This package provides commission management for Laravel admin panels. It supports global and category-based commissions, percentage or fixed values, and soft delete functionality.

## Features
- CRUD for commissions
- Commission types: global, category
- Commission calculation: percentage, fixed
- Soft delete support
- Admin panel integration
- AJAX-powered dynamic form fields

## Migration
- Table: `commissions`
- Fields: `type`, `category_id`, `commission_type`, `commission_value`, `status`, timestamps, soft deletes

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

## AJAX Dynamic Fields
- Changing the commission type in the form fetches categories dynamically.

## Soft Delete
- Deleted commissions are not removed from the database, but marked as deleted.

## Customization
- Views can be published and customized.
- Extend controller logic as needed.

## License
MIT
