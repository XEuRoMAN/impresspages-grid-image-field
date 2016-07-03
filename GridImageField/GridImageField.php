<?php

namespace Plugin\GridImageField;


class GridImageField extends \Ip\Internal\Grid\Model\Field {
    protected $label = '';
    protected $field = '';
    protected $repositoryBindKey = 'Grid';
    protected $defaultValue = '';
    protected $repositoryPath = '';
    protected $destinationPath = 'file/repository/';
    protected $reflectionOptions = array();

    public function __construct($field_config, $whole_config) {
        parent::__construct($field_config, $whole_config);

        if (!empty($field_config['repositoryBindKey'])) {
            $this->repositoryBindKey = $field_config['repositoryBindKey'];
        } else {
            $this->repositoryBindKey = 'Table_' . $whole_config['table'] . '_' . $this->field;
        }

        if (!empty($this->defaultValue) && !is_array($this->defaultValue)) {
            $this->defaultValue = array($this->defaultValue);
        }

        if (array_key_exists('repositoryPath', $field_config)) {
            $this->repositoryPath = $field_config['repositoryPath'];
        }

        if (array_key_exists('destinationPath', $field_config)) {
            $this->destinationPath = $field_config['destinationPath'];
        }

        if (array_key_exists('reflectionOptions', $field_config)) {
            $this->reflectionOptions = $field_config['reflectionOptions'];
        }
    }

    public function preview($record_data) {
        if ('' != $record_data[$this->field]) {
            $image_src = ipFileUrl($this->destinationPath . $record_data[$this->field]);

            return "<img style='max-height: 70px;' src='$image_src' alt='".$record_data[$this->field]."' />";
        }

        return '---';
    }

    public function createField() {
        $field = new \Plugin\GridImageField\ImageField(array(
            'label' => $this->label,
            'name' => $this->field,
            'path' => $this->repositoryPath,
            'destinationPath' => $this->destinationPath
        ));

        $field->setValue($this->defaultValue);

        return $field;
    }

    public function createData($post_data) {
        if (isset($post_data[$this->field])) {
            $file_name = $post_data[$this->field][0];
            $source_path = ipFile('file/repository/' . $file_name);
            $destination_path = ipFile($this->destinationPath . $file_name);

            $data = array(
                'source' => $source_path,
                'destination' => $destination_path,
                'options' => $this->reflectionOptions
            );

            ipJob('ipCreateReflection', $data);

            return array($this->field => $file_name);
        }

        return array($this->field => null);
    }


    public function updateField($record_data) {
        $field = new \Plugin\GridImageField\ImageField(array(
            'label' => $this->label,
            'name' => $this->field,
            'path' => $this->repositoryPath,
            'destinationPath' => $this->destinationPath
        ));

        $field->setValue($record_data[$this->field]);

        return $field;
    }

    public function updateData($post_data) {
        if (isset($post_data[$this->field])) {
            $file_name = $post_data[$this->field][0];
            $source_path = ipFile('file/repository/' . $file_name);
            $destination_path = ipFile($this->destinationPath . $file_name);

            $data = array(
                'source' => $source_path,
                'destination' => $destination_path,
                'options' => $this->reflectionOptions
            );

            ipJob('ipCreateReflection', $data);

            return array($this->field => $file_name);
        }

        return array($this->field => null);
    }

    public function searchField($search_variables) {
        $field = new \Ip\Form\Field\Text(array(
            'label' => $this->label,
            'name' => $this->field,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));

        if (!empty($search_variables[$this->field])) {
            $field->setValue($search_variables[$this->field]);
        }

        return $field;
    }

    public function searchQuery($search_variables) {
        if (isset($search_variables[$this->field]) && $search_variables[$this->field] !== '') {
            return '`' . $this->field . '` like ' . ipDb()->getConnection()->quote(
                '%' . $search_variables[$this->field] . '%'
            ) . ' ';
        }

        return null;
    }




    public function afterCreate($record_id, $record_data) {
        if (!empty($record_data[$this->field])) {
            $this->bindOriginalFile($record_data[$this->field][0], $record_id);
        }
    }

    public function afterUpdate($record_id, $old_data, $new_data) {
        if (!isset($old_data[$this->field])) {
            $old_data[$this->field] = '';
        }
        if (!isset($new_data[$this->field])) {
            $new_data[$this->field] = '';
        }


        if (!empty($old_data[$this->field]) && $old_data[$this->field] != $new_data[$this->field]) {
            $this->unbindOriginalFile($old_data[$this->field], $record_id);
        }

        if (!empty($new_data[$this->field]) && $old_data[$this->field] != $new_data[$this->field]) {
            $this->bindOriginalFile($new_data[$this->field][0], $record_id);
        }
    }

    public function afterDelete($record_id, $record_data) {
        $file_name = $record_data[$this->field];

        $this->unbindOriginalFile($file_name, $record_id);
    }



    private function bindOriginalFile($file_name, $record_id) {
        if (!empty($file_name)) {
            \Ip\Internal\Repository\Model::bindFile(
                $file_name, $this->repositoryBindKey, $record_id, $this->repositoryPath
            );
        }
    }

    private function unbindOriginalFile($file_name, $record_id) {
        if (!empty($file_name)) {
            \Ip\Internal\Repository\Model::unbindFile(
                $file_name, $this->repositoryBindKey, $record_id, $this->repositoryPath
            );
        }
    }
}
