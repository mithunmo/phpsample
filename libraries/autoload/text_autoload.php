<?php
/**
 * system Autoload component
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 */
return array(
	'textDiff'				=> 'textDiff/textDiff.class.php',
	
	'Text_Diff'				=> 'textDiff/Diff.php',
	'Text_Diff3'			=> 'textDiff/Diff3.php',
	'Text_MappedDiff'		=> 'textDiff/Diff.php',
	'Text_Diff_Op'			=> 'textDiff/Diff.php',
	'Text_Diff_Op_copy'		=> 'textDiff/Diff.php',
	'Text_Diff_Op_delete'	=> 'textDiff/Diff.php',
	'Text_Diff_Op_add'		=> 'textDiff/Diff.php',
	'Text_Diff_Op_change'	=> 'textDiff/Diff.php',

	'Text_Diff_Mapped'		=> 'textDiff/Text/Diff/Mapped.php',
	'Text_Diff_Renderer'	=> 'textDiff/Text/Diff/Renderer.php',
	'Text_Diff_ThreeWay'	=> 'textDiff/Text/Diff/ThreeWay.php',
	
	'Text_Diff_Engine_native'		=> 'textDiff/Text/Diff/Engine/native.php',
	'Text_Diff_Engine_shell'		=> 'textDiff/Text/Diff/Engine/shell.php',
	'Text_Diff_Engine_string'		=> 'textDiff/Text/Diff/Engine/string.php',
	'Text_Diff_Engine_xdiff'		=> 'textDiff/Text/Diff/Engine/xdiff.php',
	
	'Text_Diff_Renderer_context'	=> 'textDiff/Text/Diff/Renderer/context.php',
	'Text_Diff_Renderer_inline'		=> 'textDiff/Text/Diff/Renderer/inline.php',
	'Text_Diff_Renderer_unified'	=> 'textDiff/Text/Diff/Renderer/unified.php',
);