# tailor-foundation

> A wordpress plugin that extends [Tailor](https://github.com/andrew-worsfold/tailor) with Foundation classes.

Supports both Flex Grids and Xy-Grids but if using Xy-grids you need to add a filter notifying `tailor-foundation` to use Xy-grid options. See the filter list below in this README.

*Note that this plugin overrides the core Tailor elements rather than defining new ones. This will unfortunately cause some instability on upstream updates.*

## Features

- Row and Columns
- Grid and Grid items
- Button
- Hero
- List (needs works)
- Posts (also exposes _post type_ and non-tailor slick instances) 
- Image (duplicate of Image element in [Tailor advanced](https://github.com/andrew-worsfold/tailor-advanced) with minor adjustments)
- Global Visibility classes
- Removes a heap of attribute options.