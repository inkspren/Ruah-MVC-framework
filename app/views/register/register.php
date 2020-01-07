<?php 
use Core\FH;
?>
<?php $this->start('head'); ?>
<?php $this->end(); ?>

<?php $this->start('body'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Register</div>
                <div class="card-body">
                    <form class="form" action="" method="post">
                        <?= FH::csrfInput(); ?>
                        <?= FH::displayErrors($this->displayErrors) ?>
                        <?= FH::inputBlock('text', 'First Name', 'fname', $this->newUser->fname, ['class'=>'form-control input-sm'], ['class'=>'form-group']) ?>
                        <?= FH::inputBlock('text', 'Last Name', 'lname', $this->newUser->lname, ['class'=>'form-control input-sm'], ['class'=>'form-group']) ?>
                        <?= FH::inputBlock('text', 'Email', 'email', $this->newUser->email, ['class'=>'form-control input-sm'], ['class'=>'form-group']) ?>
                        <?= FH::inputBlock('text', 'Username', 'username', $this->newUser->username, ['class'=>'form-control input-sm'], ['class'=>'form-group']) ?>
                        <?= FH::inputBlock('password', 'Password', 'password', $this->newUser->password, ['class'=>'form-control input-sm'], ['class'=>'form-group']) ?>
                        <?= FH::inputBlock('password', 'Confirm Password', 'confirm', $this->newUser->getConfirm(), ['class'=>'form-control input-sm'], ['class'=>'form-group']) ?>
                        <?= FH::submitBlock('Register', ['class'=>'btn btn-primary'], ['class'=>'float-right']) ?>
                    </form>    
                </div>    
            </div>
        </div>
    </div>
</div>                            
<?php $this->end(); ?>