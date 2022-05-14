# minimize-prep

This repository contains a PHP script that reads a file where HTML tags (`<script>` and '<link>') are used for including CSS and Javascript files in a web page. As each CSS or JavaScript file tag is found the file contents are read and then appended to output file (CSS or JS).

The result are two files that are ready for *minimization*. However, minimization is not required. You can use this to combine your CSS and JS files in order to reduce the number of requests made by the browser.

## What's Here

* `minprep.json` - script configuration
* `minprep.php` - opens the specified (*in minprep.json*) HTML file, reads it and looks for `<link>` and `<script>` tags. When found the path and file name are extracted and those files will be opened and concatenated in the specified CSS and JS files.

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
    "jsout": "./site.js"
}
```

Please note that `"fileroot"` is set for use within this repository space. Edit it as needed for your project. But for now leave it as-is.

# Run

```
php ./minprep.php
```



