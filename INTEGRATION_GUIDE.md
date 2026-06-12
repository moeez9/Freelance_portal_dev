# Laravel Blade Template Integration Guide

Follow these steps to integrate the converted templates into your Laravel project.

## 1. File Placement

Extract the provided zip file and copy the contents to your Laravel project as follows:

| Source Directory | Target Laravel Directory | Description |
| :--- | :--- | :--- |
| `resources/views/` | `resources/views/` | All Blade templates and layouts. |
| `public/assets/` | `public/assets/` | Images, original CSS, and JS files. |
| `routes/web.php` | `routes/web.php` | Route definitions for all pages. |
| `resources/css/app.css` | `resources/css/app.css` | Tailwind directives and custom styles. |
| `resources/js/app.js` | `resources/js/app.js` | Alpine.js and bootstrap logic. |
| `tailwind.config.js` | Root directory | Tailwind CSS configuration. |
| `vite.config.js` | Root directory | Vite configuration. |
| `postcss.config.js` | Root directory | PostCSS configuration. |
| `package.json` | Root directory | NPM dependencies and scripts. |

## 2. Install Dependencies

Open your terminal in the project root and run:

```bash
npm install
```

This will install Tailwind CSS, Alpine.js, Vite, and other necessary tools.

## 3. Compile Assets

To compile the assets for development with hot-reloading:

```bash
npm run dev
```

To compile the assets for production:

```bash
npm run build
```

## 4. Verify Routes

The `routes/web.php` file contains basic closures to return the views. You can later move these to Controllers as your project grows.

Example:
```php
Route::get('/', function () {
    return view('index');
});
```

## 5. Important Notes

*   **Asset Helpers**: All images and CSS/JS files are linked using `{{ asset('assets/...') }}`. Ensure your `APP_URL` in the `.env` file is correctly set.
*   **Tailwind & Alpine**: The project is set up to use Vite. If you are using an older version of Laravel (Mix), you will need to adapt the `vite.config.js` to `webpack.mix.js`.
*   **Original CSS**: We have included `output-tailwind.css` and `output-scss.css` in the `public/assets/css` folder to maintain the original design's alignment. These are loaded in `layouts/app.blade.php`.

## 6. Troubleshooting

*   **Layout Broken?**: Ensure `npm run build` has been executed and that the `public/assets` folder contains all the original CSS files.
*   **Images Missing?**: Check if the images exist in `public/assets/images` and that the paths in Blade templates match.

---
**Happy Coding!**
