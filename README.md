# minimize-prep

This repository contains a PHP script that reads a file where HTML tags (`<script>` and `<link>`) are used for including CSS and Javascript files in a web page. As each CSS or JavaScript file tag is found the file contents are read and then appended to an appropriate output file (CSS or JS). 

In any `<script>` and `<link>` tags where the resource is not local those will be skipped and their contents will not be appended to output files (CSS or JS). 

The result are two files that are ready for *minimization*. However, minimization is not required. You can use this to combine your CSS and JS files in order to reduce the number of requests made by the browser to the server.

## What's Here

* `minprep.json` - script configuration
* `minprep.php` - opens the specified (*in minprep.json*) HTML file, reads it and looks for `<link>` and `<script>` tags. When found the path and file name are extracted and those files will be opened and concatenated in the specified CSS and JS files.
* `minimize-prep.php` - Contains the functions used by `minprep.php`.
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
│   │   └── *.css
│   │
│   ├── js
│   │   └── *.js
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
    "cssexclude": ["cssexcl"],
    "jsexclude": ["jsexcl"],
    "cssout": "site.css",
    "jsout": "site.js",
    "mkremove": true,
    "rmvscript": "rmvresources.sh"
}
```

Please note that `"fileroot"` is set for use within this repository space. Edit it as needed for your project. But for now leave it as-is.

The remaining settings are:

* `"cssexclude": ["cssexcl"]` and `"jsexclude": ["jsexcl"]` - Each is an array of strings where each is compared against the current resource found in `./public_html/example.html`. If there is a *partial* match then that resource will be excluded from the `./site.css` or `./site.js` files.
* `"cssout": "./site.css"` and `"jsout": "./site.js"` - The path + file names of the resulting output files. 
* `"mkremove": true` - When `true` a file named in `"rmvscript"` will be created. It will contain Linux `rm` commands for each of the resource files found.

# Run

```
php ./minprep.php
```

The script will create output similar to this:

```
Starting preparation...
Input: ./public_html/example.html
Files Root Path: ./public_html/
./public_html/assets/css/site.css and ./public_html/assets/js/site.js will be overwritten.

Creating rmvresources.sh file

Excluded - <link rel="stylesheet" href="https://unpkg.com/reset-css@5.0.1/reset.css" />
Found - ./assets/css/example_1.css
Found - ./assets/css/example_2.css
Found - assets/css/example_3.css
Excluded - <link rel="stylesheet" href="./assets/css/cssexclude_1.css" type="text/css" />
Excluded - <link rel="stylesheet" href="./assets/cssexcl/exclude_1.css" type="text/css" />
Excluded - <script src="//code.jquery.com/jquery-3.6.0.min.js" type="text/javascript"></script>
Found - /assets/js/example_1.js
Found - ./assets/js/example_2.js
Found - ./assets/js/example_3.js
Found - ./assets/js/example_4.js
Excluded - <script src="/assets/js/jsexclude_1.js" type="text/javascript"></script>
Excluded - <script src="/assets/jsexcl/exclude_1.js" type="text/javascript"></script>

Preparation Complete.
```

# Output

The current configuration will cause 3 files to be created:

* `site.css` and `site.js`
* `rmvresources.sh` - An optional bash script file that will contain Linux `rm` commands to delete the CSS and JSS *source* files that were used to create `site.css` and `site.js`. **USE THIS SCRIPT WITH CAUTION!!!**

To disable the creation of `rmvresources.sh` edit `minprep.json` and change `"mkremove"` to `false`.

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

All `<link>` and `<script>` tags need to be contained on a single line, for example this will **not** work:

```
<script
src="path/to/some.js"
>
```

The paths in the `<link>` and `<script>` can be *relative* or *absolute*. The script will create a path using `"fileroot"`in the `minprep.json` file.

Each time you run `minprep.php` it will **overwrite** the `site.css` and `site.js` files. And if `"mkremove"` is `true` in `minprep.json` then a *bash script* named in `"rmvscript"`  will be created. Its purpose is to make removing the original CSS and JS easier. Although removing them is not required, it is your descision for your project.

---
<img src="http://webexperiment.info/extcounter/mdcount.php?id=minimize-prep">
