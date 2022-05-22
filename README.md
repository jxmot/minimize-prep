# minimize-prep

This repository contains a PHP script that reads a file where HTML tags (`<script>` and `<link>`) are used for including CSS and Javascript files in a web page. As each CSS or JavaScript file tag is found the file contents are read and then appended to output file (CSS or JS).

The result are two files that are ready for *minimization*. However, minimization is not required. You can use this to combine your CSS and JS files in order to reduce the number of requests made by the browser to the server.

## What's Here

* `minprep.json` - script configuration
* `minprep.php` - opens the specified (*in minprep.json*) HTML file, reads it and looks for `<link>` and `<script>` tags. When found the path and file name are extracted and those files will be opened and concatenated in the specified CSS and JS files.
* `public_html/*` - this folder contains files for use in the example

## Set Up

Somewhere on your PC you have a "web page" project. And how it is organized could be different for everyone. Personally I prefer to organize my web projects like this:

```
/
└── public_html
    │
    ├── assets
    │   │
    │   ├── css
    │   │   └─ *.css
    │   │
    │   └── js
    │       └─ *.js
    │
    └── *.html
```

So this repository will use that folder structure in the example:

```
/
├── public_html
│   │
│   ├── css
│   │   └── example_*.css
│   │
│   ├── js
│   │   └── example_*.js
│   │
│   └── example.html
│
├── minprep.json
│
└── minprep.php
```

Now open `minprep.json`:

```
{
    "input":"example.html",
    "fileroot":"./public_html/",
    "cssout": "./site.css",
    "jsout": "./site.js",
    "filecomment": true,
    "mkbash": true
}
```

Please note that `"fileroot"` is set for use within this repository space. Edit it as needed for your project. But for now leave it as-is.

# Run

```
php ./minprep.php
```

The script will create output similar to this:

```
Starting preparation...
Input: ./public_html/example.html
Files Root Path: ./public_html/
./site.css and ./site.js will be overwritten.
Creating ./rmvresources.sh file

CSS found - ./assets/css/example_1.css
CSS found - ./assets/css/example_2.css
CSS found - ./assets/css/example_3.css
JS found - ./assets/js/example_1.js
JS found - ./assets/js/example_2.js
JS found - ./assets/js/example_3.js
JS found - ./assets/js/example_4.js

Preparation Complete.
```

# Important Things to Note

Commented out `<link>` or `<script>` tags must look like this:

```
<!-- <link rel="stylesheet" href="./assets/css/example_4.css" type="text/css" /> -->
```

This will **not** work:

```
<!-- 
<link rel="stylesheet" href="./assets/css/example_4.css" type="text/css" /> 
-->
```

All `<link>` and `<script>` tags need to be contained on a single line, for example this will not work:

```
<script
src="path/to/some.js"
>
```

The paths in the `<link>` and `<script>` can be *relative* or *absolute*. The script will create path using `"fileroot"`in the `minprep.json` file.

Each time you run `minprep.php` it will **overwrite** the `site.css` and `site.js` files. And if `"mkbash"` is `true` in `minprep.json` then a *bash script* named `rmvresources.sh` will be created. Its purpose is to make removing the original CSS and JS easier. Although removing them is not required, it is your descision for your project.

---
<img src="http://webexperiment.info/extcounter/mdcount.php?id=minimize-prep">
