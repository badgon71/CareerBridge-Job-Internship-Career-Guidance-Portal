# CareerBridge MVC Team Starter

This package reorganizes the existing prototype into the MVC structure requested for group collaboration.

## Structure
- `Student/Model`, `Student/View`, `Student/Controller`
- `Employer/Model`, `Employer/View`, `Employer/Controller`
- `Mentor/Model`, `Mentor/View`, `Mentor/Controller`
- `Admin/Model`, `Admin/View`, `Admin/Controller`
- `Common/` for authentication, database, shared CSS/JS, session and uploads

## What already works
- Existing role UI prototype
- Shared basic CSS
- Client-side validation
- Real common registration/login authentication
- Password hashing and password verification
- Session creation and role-based page protection
- MySQL connection file
- Database schema
- Demo accounts for local testing

## What each role member must still implement
The role Model and Controller files intentionally contain TODO methods.
Each member should implement:
- server-side validation
- profile update
- change password
- their assigned CRUD
- redirects and success/error messages
- ownership checks

The existing role forms marked `data-prototype="true"` validate in the browser but do not submit yet.
When a member connects a form to their Controller:
1. add `method="post"`
2. add the Controller `action`
3. remove `data-prototype="true"`

## XAMPP setup
1. Clone/extract to:
   `C:\xampp\htdocs\CareerBridge`
2. Start Apache and MySQL.
3. Import:
   `Common/database/careerbridge.sql`
4. Open:
   `http://localhost/CareerBridge/`

## Demo accounts
- Student: `student@example.com` / `Student@123`
- Employer: `employer@example.com` / `Employer@123`
- Mentor: `mentor@example.com` / `Mentor@123`
- Admin: `admin@example.com` / `Admin@123`

## Validation
`Common/js/validation.js` handles client-side validation.
Important validation must also be repeated in each PHP Controller before the Model is called.

See `TEAM_GIT_PLAN.md` before pushing to GitHub.
