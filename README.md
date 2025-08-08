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
    composer require <vendor>/commissions
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
# Admin FAQ Manager

This package provides an Admin FAQ Manager for managing Frequently Asked Questions (FAQs) within your application.

---

## Features

- Create, edit, and delete FAQ entries
- Organize FAQs by categories
- CKeditor support for answers
- SEO-friendly URLs and metadata for FAQ pages
- User permissions and access control

---

## Requirements

- PHP >=8.2
- Laravel Framework >= 12.x

---

## Requirements

- PHP >=8.2
- Laravel Framework >= 12.x

## Installation

### 1. Add Git Repository to `composer.json`

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/pavanraj92/admin-faqs.git"
    }
]
```

### 2. Require the package via Composer
    ```bash
    composer require admin/faqs:@dev
    ```

### 3. Publish assets
    ```bash
    php artisan faq:publish --force
    ```
---

## Usage

1. **Create**: Add a new FAQ with question and answer.
2. **Read**: View all FAQs in a paginated list.
3. **Update**: Edit FAQ information.
4. **Delete**: Remove FAQs that are no longer needed.

## Example Endpoints

| Method | Endpoint      | Description        |
|--------|---------------|--------------------|
| GET    | `/faqs`       | List all faqs      |
| POST   | `/faqs`       | Create a new faq   |
| GET    | `/faqs/{id}`  | Get faq details    |
| PUT    | `/faqs/{id}`  | Update a faq       |
| DELETE | `/faqs/{id}`  | Delete a faq       |

---

## Protecting Admin Routes

Protect your routes using the provided middleware:

```php
Route::middleware(['web','admin.auth'])->group(function () {
    // Admin FAQ routes here
});
```
---

## Database Tables

- `faqs` - Stores FAQ information

---

## License

This package is open-sourced software licensed under the MIT license.
