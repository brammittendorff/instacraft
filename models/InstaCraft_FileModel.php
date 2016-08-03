<?php
namespace Craft;

class InstaCraft_FileModel extends BaseModel
{
    /**
     * The defineAttributes of the InstaCraft_FileModel
     * @return array A folderId of the instagram url to go to
     */
    public function defineAttributes()
    {
        return array(
            'folderId' => AttributeType::Number,
            'imageId' => AttributeType::String,
            'url' => AttributeType::String,
            'text' => AttributeType::String,
        );
    }
}
