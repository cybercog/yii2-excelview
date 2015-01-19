<?php

namespace sateler;

use Yii;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

class ExcelView extends GridView {
    public $asButtonDropdown = true;
    public $forceDownload = false;
    public $exportMenu = [
        ExportMenu::FORMAT_EXCEL_X => [
            'label' => 'Exportar a Excel',
            'icon' => 'floppy-save',
            'linkOptions'=>[],
            'options' => ['title' => 'Exportar a Excel'],
            'confirmMsg' => '',
            'mime' => 'application/vnd.ms-excel',
            'extension' => 'xlsx',
            'writer' => 'Excel2007',
            'formatOptions' => [
                'sheetTitle' => 'Hoja1',
            ]
        ],
        ExportMenu::FORMAT_CSV => null,
        ExportMenu::FORMAT_EXCEL => null,
        ExportMenu::FORMAT_HTML => null,
        ExportMenu::FORMAT_PDF => null,
        ExportMenu::FORMAT_TEXT => null,
        ];
    public $export = false;
    
    public $layout = "{fullExport}\n{summary}\n{items}\n{pager}";
    
    public $filename;

    private $_columns;
    private $_fullExport;
    
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->_columns = $config['columns'];
	$this->_fullExport = ExcelMenu::widget([
                    'dataProvider' => $this->dataProvider,
                    'filterModel' => $this->filterModel,
                    'columns' => $this->_columns,
                    'asDropdown' => $this->asButtonDropdown,
                    'exportConfig' => $this->exportMenu,
		    'showConfirmAlert' => false,
		    'target' => ExportMenu::TARGET_BLANK,
		    'filename' => $this->filename ?: $this->view->title ?: 'grid-export',
		    'enableFormatter' => false,
                ]);
    }
    
    function init() {
        parent::init();
        if(!isset($this->replaceTags['{fullExport}'])) {
            $this->replaceTags['{fullExport}'] = function ($self) {
                /** @var $self ExcelView */
                return $self->_fullExport;
            };
        }
    }
}

class ExcelMenu extends ExportMenu {
	function run() {
		// We need this because this is a widget within a widget
		// Thus there has been an extra ob_start()
        $download = !empty($_POST) &&
            !empty($_POST[$this->exportRequestParam]) &&
            $_POST[$this->exportRequestParam];
        if ($download) {
            ob_end_clean();
        }
		return parent::run();
	}
}
