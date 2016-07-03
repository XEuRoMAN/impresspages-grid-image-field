<?php

namespace Plugin\GridImageField;

use Ip\Form\Field;


/**
 * Meaningful only in admin interface as public visitors can't access.
 */
class ImageField extends Field {
    protected $fileLimit = 1;
    protected $preview = 'thumbnails'; // List or thumbnails.
    protected $secure = false;
    protected $path = '';
    protected $destinationPath = '';
    protected $filter = 'image';
    protected $filterExtensions = array('jpg', 'jpeg', 'png', 'gif');

    public function __construct($options = array()) {
        if (isset($options['path'])) {
            $this->path = $options['path'];
        }

        if (isset($options['destinationPath'])) {
            $this->destinationPath = $options['destinationPath'];
        }

        parent::__construct($options);
    }

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment) {
        $data = array(
            'attributesStr' => $this->getAttributesStr($doctype),
            'classes' => implode(' ', $this->getClasses()),
            'inputName' => $this->getName(),
            'fileLimit' => $this->fileLimit,
            'value' => $this->value,
            'preview' => $this->preview,
            'secure' => $this->secure,
            'path' => $this->path,
            'destinationPath' => $this->destinationPath,
            'filter' => $this->filter,
            'filterExtensions' => $this->filterExtensions
        );

        $viewFile = 'imageFieldView.php';
        $view = ipView($viewFile, $data);

        return $view->render();
    }
}
