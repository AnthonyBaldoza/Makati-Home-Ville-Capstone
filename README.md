# Makati Home Ville Community Management System

A web-based community management system for the Makati Home Ville HOA (Dayap, Calauan, Laguna), built with Laravel and PostgreSQL. This is the midterm slice of our capstone project for COMP 016 Web Development.

**Group members:** Anthony Baldoza, Dexilou Barotia, Aaron Nidea

## Features (midterm scope)

- Registration, login, and logout. Residents register with their block, lot, and mobile number.
- Three roles: **HOA Admin**, **Resident**, and **Facility Staff**, enforced by route middleware
- **Announcements:** the Admin creates, edits, and deletes; all logged-in users can read
- **Complaints:** Residents file complaints and see only their own; the Admin sees all and updates the status (Pending, In Progress, Resolved)

Planned after the midterm: facility reservation with conflict detection, emergency alerts with SMS, and SMS help requests. The Facility Staff role exists but has no features yet.

## Requirements

- PHP 8.3 or newer
- Composer
- PostgreSQL 17 (with the `pdo_pgsql` PHP extension enabled)

## Setup

1. Clone the repository and install dependencies:
```bash
   git clone https://github.com/AnthonyBaldoza/Makati-Home-Ville-Capstone.git
   cd Makati-Home-Ville-Capstone
   composer install
```
2. Create the environment file and application key:
```bash
   copy .env.example .env
   php artisan key:generate
```
3. In pgAdmin, create an empty database named `mhv`.
4. Edit `.env` so the database lines read:
```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=mhv
   DB_USERNAME=postgres
   DB_PASSWORD=your_postgres_password
```
5. Run the migrations and seeders from a clean database:
```bash
   php artisan migrate:fresh --seed
```
6. Start the server and open http://127.0.0.1:8000:
```bash
   php artisan serve
```

## Demo accounts

| Role | Email | Password |
|---|---|---|
| HOA Admin | admin@mhv.test | admin12345 |
| Resident | resident@mhv.test | resident12345 |
| Facility Staff | staff@mhv.test | staff12345 |

New accounts created through the registration page are always Residents.

## Database design

| Table | Purpose |
|---|---|
| `roles` | HOA Admin, Resident, Facility Staff |
| `users` | accounts, with a `role_id` foreign key to `roles`, plus block, lot, and mobile number for residents |
| `announcements` | posted by an Admin (`user_id` foreign key) |
| `complaints` | filed by a Resident (`user_id` foreign key), `status` defaults to Pending |

Design decisions:

- **Separate `roles` table** instead of a role column: the foreign key prevents invalid roles, role names are stored once (normalization), and adding a new role is one new row. Trade-off: one extra table and a join.
- **`restrictOnDelete` on foreign keys:** announcements and complaints are HOA records, so a user with records cannot be deleted by accident.
- **Complaint status is a string**, validated with `Rule::in([...])`, because there are only three fixed values. A lookup table would be more normalized but heavier than needed here.
- **Block, lot, and mobile number are nullable** because Admin and Staff accounts do not live in a lot. The mobile number is unique because the SMS module will use it to identify who sent a help request.
- **Profile fields were added in a separate migration** instead of editing the original users migration, so existing databases can be updated without being rebuilt.

## Security decisions

- Role checks happen on the server through the `role` middleware, not only by hiding buttons in views.
- `role_id` is never taken from the registration form; the server assigns Resident.
- Complaint `user_id` and `status` are not mass assignable; `user_id` is set through the `complaints()` relationship and `status` is changed only by the Admin-only route.
- A Resident who opens another Resident's complaint by URL receives a 403.
- Forms use `@csrf`, passwords are hashed, and output is escaped with `{{ }}`.
- Registration validates the mobile number format (`09` followed by 9 digits) and rejects duplicates.

## Project structure

- `routes/web.php`: routes and middleware groups
- `app/Http/Controllers/`: `AuthController`, `AnnouncementController`, `ComplaintController`
- `app/Http/Middleware/EnsureUserHasRole.php`: role check (registered as `role`)
- `app/Models/`: `Role`, `User`, `Announcement`, `Complaint`
- `database/migrations/` and `database/seeders/`: schema and demo data
- `resources/views/`: Blade views using the shared `layouts/app.blade.php`
- `public/css/app.css`: plain CSS, no build step required