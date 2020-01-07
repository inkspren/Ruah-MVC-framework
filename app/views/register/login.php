<?php 
use Core\FH;
?>
<?php $this->start('head'); ?>
<?php $this->end(); ?>

<?php $this->start('body'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <form class="form" action="<?=PROOT?>register/login" method="post">
                    <?= FH::csrfInput() ?>
                    <?= FH::displayErrors($this->displayErrors) ?>
                    <?= FH::inputBlock('text', 'Username', 'username', $this->login->username,['class'=>'form-control'],['class'=>'form-group']) ?>
                    <?= FH::inputBlock('password', 'Password', 'password', $this->login->password,['class'=>'form-control'],['class'=>'form-group']) ?>
                    <?= FH::checkboxBlock('Remember Me', 'remember_me', $this->login->getRememberMeChecked(),['class'=>'form-check-input'],['class'=>'form-check']) ?>   
                    <?= FH::submitBlock('Login', ['class'=>'btn btn-primary'],['class'=>'form-group']) ?>
                    <div class="text-right">
                        <a href="<?=PROOT?>register/register" class="text-primary">Register</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->end(); ?>
