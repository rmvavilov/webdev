<?php
function buildTree(array $messages, $parent_id)
{
    $result = array();
    foreach ($messages as $message) {
        if ($message['parent_id'] == $parent_id) {
            $children = buildTree($messages, $message['id']);
            if ($children) {
                $message['children'] = $children;
            }
            $result[] = $message;
        }
    }
    return $result;
}