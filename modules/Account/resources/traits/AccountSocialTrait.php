<?php
/**
 * AccountSocialTrait.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\modules\Account\resources\traits;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Trait AccountSocialTrait
 * @package cookyii\modules\Account\resources\traits
 */
trait AccountSocialTrait
{

    /**
     * @param \yii\authclient\ClientInterface $Client
     * @return bool
     * @throws \yii\web\ServerErrorHttpException
     */
    public function pushSocialLink(\yii\authclient\ClientInterface $Client)
    {
        /** @var \cookyii\modules\Account\resources\Account $self */
        $self = $this;

        $attributes = $Client->getUserAttributes();

        $credentials = [
            'account_id' => (integer)$self->id,
            'social_id' => (string)$attributes['id'],
        ];

        $socialnetwork = $Client->getId();

        /** @var $class \cookyii\modules\Account\resources\AccountAuth */
        $class = \Yii::createObject(\cookyii\modules\Account\resources\AccountAuth::className());

        /** @var \cookyii\modules\Account\resources\AccountAuth $Auth */
        $Auth = $class::find()
            ->byAccountId($credentials['account_id'])
            ->bySocialType($socialnetwork)
            ->bySocialId($credentials['social_id'])
            ->one();

        if (empty($Auth)) {
            $Auth = new $class($credentials);
            $Auth->social_type = $socialnetwork;
        }

        if ($Client instanceof \yii\authclient\BaseOAuth) {
            $Token = $Client->getAccessToken();
            $token = ArrayHelper::toArray($Token);
            $token['params'] = $Token->getParams();

            $Auth->token = Json::encode($token);
        }

        $Auth->validate() && $Auth->save();

        return $Auth;
    }

    /**
     * @param \yii\authclient\ClientInterface $Client
     * @throws \yii\base\NotSupportedException
     */
    public function appendClientAttributes(\yii\authclient\ClientInterface $Client)
    {
        /** @var \cookyii\modules\Account\resources\Account $self */
        $self = $this;

        $attributes = $Client->getUserAttributes();

        switch ($Client->getId()) {
            default:
                $attributes = null;
                break;
            case 'facebook':
                $attributes = $this->appendFacebookAttributes($attributes);
                break;
            case 'instagram':
                $attributes = $this->appendInstagramAttributes($attributes);
                break;
            case 'github':
                $attributes = $this->appendGithubAttributes($attributes);
                break;
            case 'google':
                $attributes = $this->appendGoogleAttributes($attributes);
                break;
            case 'linkedin':
                $attributes = $this->appendLinkedinAttributes($attributes);
                break;
            case 'live':
                $attributes = $this->appendLiveAttributes($attributes);
                break;
            case 'twitter':
                $attributes = $this->appendTwitterAttributes($attributes);
                break;
            case 'vkontakte':
                $attributes = $this->appendVkontakteAttributes($attributes);
                break;
            case 'yandex':
                $attributes = $this->appendYandexAttributes($attributes);
                break;
            case 'odnoklassniki':
                $attributes = $this->appendOdnoklassnikiAttributes($attributes);
                break;
        }

        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $attr = $self->getAttribute($key);

                if ($self->hasAttribute($key) && empty($attr)) {
                    $self->setAttribute($key, $value);
                }
            }
        }
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendFacebookAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['name']) && !empty($attributes['name'])) {
            $name = $attributes['name'];
        }

        if (isset($attributes['email']) && !empty($attributes['email'])) {
            $login = $email = $attributes['email'];
        }

        return [
            'login' => $login,
            'name' => trim($name),
            'email' => $email,
            'avatar' => $avatar,
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendInstagramAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['username']) && !empty($attributes['username'])) {
            $login = $attributes['username'];
        }

        if (isset($attributes['full_name']) && !empty($attributes['full_name'])) {
            $name = $attributes['full_name'];
        }

        if (isset($attributes['profile_picture']) && !empty($attributes['profile_picture'])) {
            $avatar = $attributes['profile_picture'];
        }

        return [
            'login' => $login,
            'name' => trim($name),
            'email' => $email,
            'avatar' => $avatar,
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendGithubAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['login']) && !empty($attributes['login'])) {
            $login = $name = $attributes['login'];
        }

        if (isset($attributes['name']) && !empty($attributes['name'])) {
            $name = $attributes['name'];
        }

        if (isset($attributes['email']) && !empty($attributes['email'])) {
            $email = $attributes['email'];
            if (empty($login)) {
                $login = $email;
            }
        }

        if (isset($attributes['avatar_url']) && !empty($attributes['avatar_url'])) {
            $avatar = $attributes['avatar_url'];
        }

        return [
            'login' => $login,
            'name' => trim($name),
            'email' => $email,
            'avatar' => $avatar,
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendGoogleAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['displayName']) && !empty($attributes['displayName'])) {
            $name = $attributes['displayName'];
        }

        if (isset($attributes['name']) && isset($attributes['name']['familyName']) && !empty($attributes['name']['familyName'])) {
            $name = $attributes['name']['familyName'] . ' ';
            if (isset($attributes['name']['givenName']) && !empty($attributes['name']['givenName'])) {
                $name .= $attributes['name']['givenName'];
            }
        }

        if (isset($attributes['emails']) && !empty($attributes['emails'])) {
            $login = $email = array_shift($attributes['emails'])['value'];
        }

        if (isset($attributes['image']) && !empty($attributes['image'])) {
            $avatar = $attributes['image']['url'];
        }

        return [
            'login' => $login,
            'name' => trim($name),
            'email' => $email,
            'avatar' => $avatar,
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendLinkedinAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['last_name']) && !empty($attributes['last_name'])) {
            $name = $attributes['last_name'] . ' ';
        }

        if (isset($attributes['first_name']) && !empty($attributes['first_name'])) {
            $name .= $attributes['first_name'];
        }

        if (isset($attributes['email']) && !empty($attributes['email'])) {
            $login = $email = $attributes['email'];
        }

        return [
            'login' => $login,
            'name' => trim($name),
            'email' => $email,
            'avatar' => $avatar,
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendLiveAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['last_name']) && !empty($attributes['last_name'])) {
            $name = $attributes['last_name'] . ' ';
        }

        if (isset($attributes['first_name']) && !empty($attributes['first_name'])) {
            $name .= $attributes['first_name'];
        }

        if (isset($attributes['emails']) && !empty($attributes['emails'])) {
            if (isset($attributes['emails']['business']) && !empty($attributes['emails']['business'])) {
                $email = $attributes['emails']['business'];
            }

            if (isset($attributes['emails']['account']) && !empty($attributes['emails']['account'])) {
                $email = $attributes['emails']['account'];
            }

            if (isset($attributes['emails']['personal']) && !empty($attributes['emails']['personal'])) {
                $email = $attributes['emails']['personal'];
            }

            if (!empty($email)) {
                $login = $email;
            }
        }

        return [
            'login' => $login,
            'name' => trim($name),
            'email' => $email,
            'avatar' => $avatar,
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendTwitterAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['screen_name']) && !empty($attributes['screen_name'])) {
            $login = $attributes['screen_name'];
        }

        if (isset($attributes['name']) && !empty($attributes['name'])) {
            $name = $attributes['name'];
        }

        if (isset($attributes['profile_image_url']) && !empty($attributes['profile_image_url'])) {
            $avatar = $attributes['profile_image_url'];
        }

        if (isset($attributes['profile_image_url_https']) && !empty($attributes['profile_image_url_https'])) {
            $avatar = $attributes['profile_image_url_https'];
        }

        return [
            'login' => $login,
            'name' => $name,
            'email' => $email,
            'avatar' => $avatar,
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendVkontakteAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['screen_name']) && !empty($attributes['screen_name'])) {
            $login = $attributes['screen_name'];
        }

        if (isset($attributes['last_name']) && !empty($attributes['last_name'])) {
            $name = $attributes['last_name'] . ' ';
        }

        if (isset($attributes['first_name']) && !empty($attributes['first_name'])) {
            $name .= $attributes['first_name'];
        }

        if (isset($attributes['photo']) && !empty($attributes['photo'])) {
            $avatar = $attributes['photo'];
        }

        return [
            'login' => $login,
            'name' => trim($name),
            'email' => $email,
            'avatar' => $avatar,
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendYandexAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['login']) && !empty($attributes['login'])) {
            $login = $attributes['login'];
        }

        if (isset($attributes['last_name']) && !empty($attributes['last_name'])) {
            $name = $attributes['last_name'] . ' ';
        }

        if (isset($attributes['first_name']) && !empty($attributes['first_name'])) {
            $name .= $attributes['first_name'];
        }

        if (isset($attributes['real_name']) && !empty($attributes['real_name'])) {
            $name = $attributes['real_name'];
        }

        if (isset($attributes['default_email']) && !empty($attributes['default_email'])) {
            $email = $attributes['default_email'];
        }

        // @todo implement yandex avatar $attributes['default_avatar_id']

        return [
            'login' => $login,
            'name' => trim($name),
            'email' => $email,
            'avatar' => $avatar,
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function appendOdnoklassnikiAttributes(array $attributes)
    {
        $login = null;
        $name = null;
        $email = null;
        $avatar = null;

        if (isset($attributes['last_name']) && !empty($attributes['last_name'])) {
            $name = $attributes['last_name'] . ' ';
        }

        if (isset($attributes['first_name']) && !empty($attributes['first_name'])) {
            $name .= $attributes['first_name'];
        }

        // @todo implement odnoklassniki avatar $attributes['photo_id']

        return [
            'login' => $login,
            'name' => trim($name),
            'email' => $email,
            'avatar' => $avatar,
        ];
    }
}
