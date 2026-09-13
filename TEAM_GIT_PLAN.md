# CareerBridge Team Git Plan

## Important
Do NOT let one member commit all four role folders. The purpose of this structure is for each member to own, implement, test and commit their own module.

## Repository
One repository: `CareerBridge`

Main branch:
- `main`

Working branches:
- `student-module`
- `employer-module`
- `mentor-module`
- `admin-module`

## Ownership
- Member 1: `Student/`
- Member 2: `Employer/`
- Member 3: `Mentor/`
- Member 4: `Admin/`

Shared files live under `Common/`. Assign one owner to a shared file before editing it.

Suggested Common ownership:
- `Common/css/style.css` + `Common/js/validation.js`: Mentor member
- `Common/Config/db.php` + `Common/database/careerbridge.sql`: Admin member
- `Common/Model/AuthModel.php` + `Common/Controller/AuthController.php` + `Common/View/*`: Employer member
- CV integration testing and `Common/uploads/cv/`: Student member

## Recommended contribution workflow

### Initial main
One member creates the repository with only:
- README.md
- .gitignore
- Common/
- index.php

Then every member clones that repository.

### Student member
1. `git checkout -b student-module`
2. Add/implement/test only `Student/`
3. `git add Student/`
4. `git commit -m "Add student MVC module"`
5. `git push -u origin student-module`
6. Open a Pull Request to `main`

### Employer member
Same process with `Employer/`.

### Mentor member
Same process with `Mentor/`.

### Admin member
Same process with `Admin/`.

Each member should make multiple meaningful commits while implementing their Controller/Model/validation rather than one copy-only commit.

## Conflict rule
- Never edit another member's role folder without agreement.
- Never have two members edit the same Common file at the same time.
- Before starting work: `git pull origin main`.
- Before PR/merge: merge latest `main` into your branch and test again.

## XAMPP
Clone into:
`C:\xampp\htdocs\CareerBridge`

Import:
`Common/database/careerbridge.sql`

Run:
`http://localhost/CareerBridge/`
