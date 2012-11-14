<?php
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$doc->addStyleSheet('../bootstrap.css');
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
	</head>
	<body>
		<h1>Tiny MVC</h1>
		<jdoc:include type="content" />
	</body>
</html>