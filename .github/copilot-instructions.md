# Copilot / AI coding instructions — livre-or

This file gives concise, actionable guidance to AI coding agents working on this repository.
Keep changes small and empirical — follow existing patterns and prefer the simplest safe fix.

## Big picture
- Tiny custom PHP MVC-like starter (no Composer/autoload). Entry point is `public/index.php`.
- Routing: `Core\\Router` maps exact paths (no parameter tokens). Routes are declared in `public/index.php` with `$router->get()` / `$router->post()` using the string format `App\\\\Controllers\\\\SomeController@method`.
- Controllers live under `app/Controllers` in the `App\\Controllers` namespace. They extend `Core\\BaseController` and use `$this->render('path/to/view', $params)` to render views.
- Views are plain PHP under `app/Views`. `render()` converts `$params` to variables and injects the view content into `app/Views/layouts/base.php` as `$content`.
- Models live under `app/Models` and use `Core\\Database::getPdo()` (PDO singleton) for DB access.

## Key conventions and patterns (do exactly like existing code)
- Routing string format: `Namespace\\\\ControllerClass@method`. The router instantiates the controller and calls the method with no arguments.
- Controller actions do not accept parameters — they read from `$_GET`, `$_POST`, `$_FILES`, and `$_SESSION` directly.
- Use `BaseController::generateCsrfToken()` to obtain a CSRF token and `verifyCsrfToken($token)` to validate. Many forms and POST handlers already expect/require this.
- Flash messages use `$_SESSION['flash']`. Set then redirect; views display them (follow existing usage).
- Authentication state: `$_SESSION['user_id']`, `$_SESSION['login']`, `$_SESSION['avatar']` are used. Check `empty($_SESSION['user_id'])` for login status.
- DB config reads env vars (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) with sane defaults; local development often uses the bundled SQL in `database/`.
- File uploads (avatars) are saved to `public/uploads/avatars` and the DB stores the public path (e.g. `/uploads/avatars/xxx.png`).

## Error handling & model contracts
- Models return arrays for found rows, `null` if not found, and `bool` for create/update/delete success. They swallow PDOExceptions and return safe defaults. Agents should follow the same calling pattern and handle null/false return values gracefully.

## Where to look for examples
- Routing & bootstrap: `public/index.php` (all routes are declared here).
- Router implementation: `core/Router.php` (exact path matching, no param parsing).
- Controller base and CSRF helpers: `core/BaseController.php` (render, generateCsrfToken, verifyCsrfToken).
- DB singleton and env vars: `core/Database.php`.
- Comment flows: `app/Controllers/CommentController.php` + `app/Models/CommentModel.php` + `app/Views/comment/*` show typical read/create/update/delete patterns and server-side validation.
- Auth flows: `app/Controllers/AuthController.php` + `app/Models/UserModel.php` + `app/Views/auth/*`.

## Quick developer workflows (how to run / test changes locally)
- The app is served from `public/`. With PHP installed you can run quickly from project root:

  php -S localhost:8000 -t public

  Then open http://localhost:8000/ (or use your Laragon setup).

- Database: run the SQL in `database/livreor.sql` (or `database/livreor_database.sql.txt`) and optionally `database/add_avatar_column.sql` to add avatar column. The Database class reads `DB_*` env vars if set.

## Small but important implementation details for agents
- There is no Composer autoloader. `public/index.php` includes the core files and controllers/models manually. If you add new controllers/models, either require them in `public/index.php` or implement an autoloader consistently across the project.
- Router matches routes exactly. If you need path parameters, modify `core/Router.php` and update `public/index.php` accordingly — **do not** add loose assumptions about parameter parsing in controllers.
- Views are vulnerable to XSS unless values are escaped. Current code uses `htmlspecialchars(...)` in views for user-displayed values — follow that pattern.
- CSRF tokens are regenerated/invalidated by `BaseController`. POST handlers call `verifyCsrfToken()` and expect tokens to be single-use.
- Logs are written to a `logs` directory created at runtime (controllers write `logs/security.log`). Keep file permissions and existence checks.

## Examples to copy/paste
- Render a view (controller):

  $this->render('comment/index', ['articles' => $comments, 'title' => 'Livre d\'or']);

- Declare a route (public/index.php):

  $router->post('/comments/new', 'App\\\\Controllers\\\\CommentController@create');

- Get PDO instance:

  $pdo = \\Core\\Database::getPdo();

## Guidance for pull requests made by AI
- Keep changes minimal and localized. Follow existing naming and error-handling patterns.
- If you add new files, update `public/index.php` (or add a simple PSR-4 autoloader and require it) so the app can be served without manual edits.
- When changing security behavior (CSRF, auth), include a short note and tests or a manual verification checklist.

## When to ask for help
- If you need to change the HTTP routing behavior (parameterized paths, middleware), ask before large refactors — this project relies on simple, explicit routing.

---
If anything in this file is unclear or you want a different level of detail (examples, tests, or a short checklist for manual verification), tell me which sections to expand.
