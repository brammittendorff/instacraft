<?php
namespace Craft;

class InstaCraft_FileModel extends BaseModel
{
    public function defineAttributes()
    {
        return array(
            'folderId' => AttributeType::Number,
            'total' => AttributeType::Number,
            'url' => AttributeType::String,
        );
    }
}
