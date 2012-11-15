TADA MVC
==========

A MVC Application using Joomla! Framework. The stucture of the application is similar to what RubyOnRails and PHPCake use.

### Application structure
    app/                     -- the application directory
      controller/            -- the controllers directory
        booksController.php  -- the controller of the Book object
      model/                 -- the model directory
        book.php             -- the Book object
      view/                  -- the view directory
        books/               -- example of resource
          index.php          -- the view file of the resource for index action
          edit.php           -- the view file of the resource of edit action
        elements/            -- the elements that are used in views
        layouts/             -- the layout that is used in the current view
        themes/              -- the themes folder
          theme_name/        -- the theme name
            elements/        -- the elements that are used in this theme
            layouts/         -- the layouts that are used in this theme
              index.php      -- the default layout is in index.php
    config/
      sql/                   
      configuration.php      -- application's config file
      routes.php              -- the routes that are used in the current application
    lib/
      tada/                  -- the code of the MVC application
    public 
      css/                   -- the CSS files
      js/                    -- the JS files
      img/                   -- the images
      index.php              -- bootstrap file
      themes/                -- the themes folder
        theme_name/          -- the name of the theme
          css/
          js/
          img/
      