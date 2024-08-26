<?php
namespace ClickBlocks\Web\UI\POM;

/**
 * Class for validate password
 *
 * @package default
 * @author
 */


class ValidatorPassword extends ValidatorRegularExpression
{
    const PASSWORD_REG_EXP = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()-+=`~,><\/\\\"'?;: {[}\]])[A-Za-z\d!@#$%^&*()-+=`~,><\/\\\"'?;: {[}\]]{12,}/";

    public function __construct($id, $message = null)
    {
        parent::__construct($id, $message);
        $this->type = 'regularexpression';
        $this->properties['expression'] = self::PASSWORD_REG_EXP;
    }
}