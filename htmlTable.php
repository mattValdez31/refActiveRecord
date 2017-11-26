<?php

class htmlTable
{
	public static function genTable($result)
	{
		echo '<table>'
		foreach ($result as $column)
		{
			echo '<tr>';
			foreach ($column as $row)
			{
				echo '<td>';
				echo $row;
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}
}

?>
