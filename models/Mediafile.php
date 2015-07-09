<?php
namespace icalab\mediafile\models;

use Yii;
use yii\db\ActiveRecord;
use icalab\mediafile\models\Mediafiletype;
use icalab\mediafile\MediafileModule;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use yii\base\Exception;

/**
 * Model for media files.
 * @property integer $id
 * @property integer $mediafiletypeid
 * @property string $title
 * @property string $notes
 * @property string $uid
 * @property integer $created_at
 * @property integer $updated_at
 */
class Mediafile extends ActiveRecord
{

    /**
     * This attribute can be used to deal with uploading files in
     * controllers and views. It is NOT used inside the model itself.
     * @var UploadedFile|Null file attribute
     */
    public $file;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mediafile}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mediafiletypeid' => Yii::t('mediafile', 'File type'),
                'title' => Yii::t('mediafile', 'Title'),
                'notes' => Yii::t('mediafile', 'notes'),
            ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if(null === $this->uid)
        {
            $this->uid = self::generateUID();
        }
        parent::init();
    }

    /**
     * Generate a path for the media file.
     * @return a path for the media file (without an extension!)
     */
    private function generateUID()
    {
        $uniqid = '';
        if (function_exists('com_create_guid') === true)
                {
                            $uniqid = trim(com_create_guid(), '{}');
                }
        // The com_create_guid function does not exist. Generate one. Code 
        // stolen from 
        // http://php.net/manual/en/function.com-create-guid.php#99425
        else
        {

            $uniqid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        }
        // Make VERY sure the hand made GUID-function above will not cause 
        // collisions.
        $uniqid = uniqid($uniqid, true);

        return md5($uniqid);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'notes', 'mediafiletypeid'], 'safe'],
            ['uid', 'required'],
            ['mediafiletypeid', 'exist', 'targetAttribute' => 'id', 'targetClass' => 'icalab\mediafile\models\Mediafiletype'],
            [['file'], 'file'],
        ];
    }

    public function getMediafiletype()
    {
        return $this->hasOne(Mediafiletype::className(), ['id' => 'mediafiletypeid']);
    }

    private $_fileData = null;

    /**
     * Return the value of the fileData attribute. If it has not been set yet,
     * an attempt is made to read it from disk.
     * NOTE: the ENTIRE file contents will be read into memory, so DO NOT use 
     * the fileData attribute to serve large files.
     *
     * @return the file data for the current file
     */
    public function getFileData()
    {
        if(null === $this->_fileData)
        {
            if(! file_exists($this->filePath))
            {
                return null;
            }
            $this->_fileData = file_get_contents($this->filePath);
        }
        return $this->_fileData;
    }

    /**
     * Return the physical path of the file.
     * @return the physical path of the file
     */
    public function getFilePath()
    {
        // Directories with huge numbers of files are very slow, so we
        // split up the file name into directory components.
        return Yii::getAlias('@webroot') 
            . '/mediafiles'
            . '/' . substr($this->uid, 0, 2)
            . '/' . substr($this->uid, 1, 2)
            . '/' . substr($this->uid, 4);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if(! parent::beforeSave($insert))
        {
            return false;
        }
        if(! file_exists($this->filePath))
        {
            throw new Exception("Attempt to save media file without storing data on disk first.");
        }

        return true;
    }

    /**
     * @inherit
     */
    public function afterDelete()
    {
        @unlink($this->filePath);
        parent::afterDelete();
    }




}
