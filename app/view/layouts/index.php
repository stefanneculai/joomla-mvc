<?php
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$doc->addStyleSheet('/css/style.css');
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
	</head>
	<body>
		<h1>Tada MVC</h1>
		<?php echo $this->element('header');?>
		<jdoc:include type="content" />
	</body>
</html>