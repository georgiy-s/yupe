<?php

/**
 * BlogBackendController контроллер для блогов в панели управления
 *
 * @author yupe team <team@yupe.ru>
 * @link http://yupe.ru
 * @copyright 2009-2013 amyLabs && Yupe! team
 * @package yupe.modules.blog.controllers
 * @since 0.1
 *
 */
class BlogBackendController extends yupe\components\controllers\BackController
{
    public function accessRules()
    {
        return array(
            array('allow', 'roles' => array('admin')),
            array('allow', 'actions' => array('create'), 'roles' => array('Blog.BlogBackend.Create')),
            array('allow', 'actions' => array('delete'), 'roles' => array('Blog.BlogBackend.Delete')),
            array('allow', 'actions' => array('index'), 'roles' => array('Blog.BlogBackend.Index')),
            array('allow', 'actions' => array('inlineEdit'), 'roles' => array('Blog.BlogBackend.Update')),
            array('allow', 'actions' => array('update'), 'roles' => array('Blog.BlogBackend.Update')),
            array('allow', 'actions' => array('view'), 'roles' => array('Blog.BlogBackend.View')),
            array('deny')
        );
    }

    public function actions()
    {
        return array(
            'inline' => array(
                'class'           => 'yupe\components\actions\YInLineEditAction',
                'model'           => 'Blog',
                'validAttributes' => array('name', 'slug', 'status', 'type')
            )
        );
    }

    /**
     * Отображает блог по указанному идентификатору
     * @throws CHttpException
     * @param  integer $id Идинтификатор блог для отображения
     *
     * @return nothing
     **/
    public function actionView($id)
    {
        if (($model = Blog::model()->loadModel($id)) !== null) {
            $this->render('view', array('model' => $model));
        } else {
            throw new CHttpException(404, Yii::t('BlogModule.blog', 'Page was not found!'));
        }
    }

    /**
     * Создает новую модель блога.
     * Если создание прошло успешно - перенаправляет на просмотр.
     *
     * @return nothing
     **/
    public function actionCreate()
    {
        $model = new Blog();

        if (Yii::app()->getRequest()->getIsPostRequest() && Yii::app()->getRequest()->getPost('Blog') !== null) {

            $model->setAttributes(Yii::app()->getRequest()->getPost('Blog'));

            if ($model->save()) {
                Yii::app()->user->setFlash(
                    yupe\widgets\YFlashMessages::SUCCESS_MESSAGE,
                    Yii::t('BlogModule.blog', 'Blog was added!')
                );
                $this->redirect(
                    (array)Yii::app()->getRequest()->getPost(
                        'submit-type',
                        array('create')
                    )
                );
            }
        }
        $this->render('create', array('model' => $model));
    }

    /**
     * Редактирование блога.
     *
     * @param  integer $id Идинтификатор блога для редактирования
     * @throw CHttpException
     * @return nothing
     **/
    public function actionUpdate($id)
    {
        if (($model = Blog::model()->loadModel($id)) === null) {
            throw new CHttpException(404, Yii::t('BlogModule.blog', 'Page was not found!'));
        }

        if (Yii::app()->getRequest()->getIsPostRequest() && Yii::app()->getRequest()->getPost('Blog') !== null) {
            $model->setAttributes(Yii::app()->getRequest()->getPost('Blog'));
            if ($model->save()) {
                Yii::app()->user->setFlash(
                    yupe\widgets\YFlashMessages::SUCCESS_MESSAGE,
                    Yii::t('BlogModule.blog', 'Blog was updated!')
                );
                $this->redirect(
                    (array)Yii::app()->getRequest()->getPost(
                        'submit-type',
                        array(
                            'update',
                            'id' => $model->id
                        )
                    )
                );
            }
        }

        $this->render('update', array('model' => $model));
    }

    /**
     * Удаляет модель блога из базы.
     * Если удаление прошло успешно - возвращется в index
     *
     * @param integer $id - идентификатор блога, который нужно удалить
     *
     * @return nothing
     **/
    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {

            // поддерживаем удаление только из POST-запроса
            if (($model = Blog::model()->loadModel($id)) === null) {
                throw new CHttpException(404, Yii::t('BlogModule.blog', 'Page was not found!'));
            }

            $model->delete();

            Yii::app()->user->setFlash(
                yupe\widgets\YFlashMessages::SUCCESS_MESSAGE,
                Yii::t('BlogModule.blog', 'Blog was deleted!')
            );

            // если это AJAX запрос ( кликнули удаление в админском grid view), мы не должны никуда редиректить
            if (!Yii::app()->getRequest()->getIsAjaxRequest()) {
                $this->redirect(Yii::app()->getRequest()->getPost('returnUrl', array('index')));
            }
        } else {
            throw new CHttpException(400, Yii::t(
                'BlogModule.blog',
                'Wrong request. Please don\'t repeate requests like this anymore!'
            ));
        }
    }

    /**
     * Управление блогами.
     *
     * @return nothing
     **/
    public function actionIndex()
    {
        $model = new Blog('search');
        $model->unsetAttributes(); // clear any default values
        if (Yii::app()->getRequest()->getParam('Blog') !== null) {
            $model->setAttributes(Yii::app()->getRequest()->getParam('Blog'));
        }
        $this->render('index', array('model' => $model));
    }
}
