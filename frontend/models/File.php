<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string $path
 * @property int $user_id
 * @property string $created_at
 * @property string|null $updated_at
 */
class File extends \yii\db\ActiveRecord
{
    const DEFAULT_DIR = 'uploads';
    const BLOCK = ['.php', '.phtml', '.php3', '.php4', '.html', '.htm'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function(){
                    return gmdate("Y-m-d H:i:s");
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type', 'path', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'type', 'path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'type' => 'Type',
            'path' => 'Path',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function uploadAttaches(array $attaches, string $dir = self::DEFAULT_DIR): array
    {
        $files = [];
        foreach ($attaches as $attach) {
            if ($file = (new static())->upload($attach, $dir)) {
                $files[] = $file;
            }
        }

        return $files;
    }

    public function upload(UploadedFile $attach, string $dir = self::DEFAULT_DIR): ?self
    {
        $path = static::getPath($dir);

        if (!is_dir($path)) {
            mkdir($path);
        }

        $this->title = $attach->baseName;
        $this->type = $attach->extension;
        $this->path = $path . '/' . time() . '-' . $attach->name;
        $this->user_id = \Yii::$app->user->getId();

        foreach (static::BLOCK as $item) {
            if (preg_match("/$item\$/i", $attach->name)) {
                return null;
            };
        }

        if ($this->validate() && $this->save()) {
            $attach->saveAs($this->path);

            return $this;
        }

        return null;
    }

    public static function getPath(string $dir): string
    {
        return Yii::$app->basePath . '/../' . $dir;
    }
}
