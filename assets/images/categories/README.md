# Category oil images

Add one JPEG image for each category in this directory. Use the category's URL slug from the admin/database as the filename, in lowercase, followed by `.jpg`.

| Category URL slug | Image path |
| --- | --- |
| `sunflower-oil` | `assets/images/categories/sunflower-oil.jpg` |
| `palm-oil` | `assets/images/categories/palm-oil.jpg` |

The examples above show the naming format. For every other category, replace the example slug with that category's URL slug. For example, a category whose URL is `refined-coconut-oil` uses `assets/images/categories/refined-coconut-oil.jpg`.

The category page automatically looks for `assets/images/categories/<category-url-slug>.jpg`. When the file is present, it appears in that category's product image panel. If it is missing, the existing category image from the admin is used as a fallback.
