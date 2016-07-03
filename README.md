# ImpressPages GridImageField plugin

Grid image plugin for [ImpressPages](https://www.impresspages.org/).

This plugin lets you upload image, crop it and move to necessary directory.

## Install

 - Upload `GridImageField` directory to your website's Plugin directory.
 - Login to the administration area.
 - Go to Plugins tab, locate `GRID IMAGE FIELD` plugin and click activate button.

## Usage

Use as one of fields in your Grid controller.

```php
array(
    'type' => 'Plugin\GridImageField\GridImageField',   // locating plugin as `type`
    'label' => 'User Photo',                            // standard ImpressPages setting
    'field' => 'photo',                                 // standard ImpressPages setting
    'repositoryPath' => 'users/',                       // if it's deeper than `file/repository`
    'destinationPath' => 'public/',                     // if you need to move cropped image somewhere else.
    'reflectionOptions' => array(                       // standard ImpressPages reflection to crop image.
        'type' => 'fit',
        'width' => 150,
        'height' => 150
    )
)
```

### Tips

 - `repositoryPath` root path is `file/repository`.
 - `destinationPath` root path is project root.
 - If used, `repositoryPath` will be added at the end of `destinationPath`, so, in above case, final destination will be `public/users/`.
 - If `destinationPath` path won't be indicated then cropped image will be placed instead of original image in `repositoryPath`.
 - If you don't need to crop image use `reflectionOptions.type => copy`.


## License

[The MIT License](http://opensource.org/licenses/MIT)