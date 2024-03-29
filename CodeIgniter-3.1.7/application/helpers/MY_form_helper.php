<?php

function form_dropdownpro($valuefield, $textfield, $name = '', $objects = array(), $selected = array(), $extra = '') {
    $options[0] = '-- Select --';
    foreach ($objects as $object) {
        $options[$object->{$valuefield}] = $object->{$textfield};
    }

    return form_dropdown($name, $options, $selected, $extra);
}

function form_radiogroup($valuefield, $textfield, $name = '', $objects = array()) {
    $result = '';

    $i = 0;
    foreach ($objects as $object) {
        $data = array('name' => $name,
            'id' => $name . $i,
            'value' => $object->{$valuefield});

        $result .= "<div>" . form_radio($data) . $object->{$textfield} . "</div>\n";
        $i++;
    }

    return $result;
}

//function form_labelpro($label_text, $id) {
//    $attributes = array('class' => 'control-label');
//    return form_label($label_text, $id, $attributes) . "\n";
//}
