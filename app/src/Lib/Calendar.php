<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /lib/Calendar.php
----------------------------------------------------------------------------- */

class Calendar {
	
	/* OPTIONS */
	protected $tableClass = 'calendar';
	protected $headingSize = 'responsive';
	protected $dayNumberLeadingZero = false;
	protected $showMonthHeading = false;
		
	private $data = array();
	
	/* OPTION GET + SET */
	public function setTableClass($tableClass){
		$this->tableClass = $tableClass;
		return $this;
	}	
	
	public function setData($data){
		$this->data = $data;
		return $this;
	}
	
	public function appendData($day, $html = ''){
		
		$this->data[$day][] = $html;
		
		return $this;
	}
	
	public function setHeadingSize($headingSize){ /* lg, md, sm*/
		$this->headingSize = $headingSize;
		return $this;
	}
	
	public function showDayNumberLeadingZero(){
		$this->dayNumberLeadingZero = true;
		return $this;
	}
	
	public function setShowMonthHeading(){ 
		$this->showMonthHeading = true;
		return $this;
	}
	
	private function getDayHeadings(){
		
		$dotw = array(
			'responsive' => array('Sun<span>day</span>','Mon<span>day</span>','Tue<span>sday</span>','Wed<span>nesday</span>','Thu<span>rsday</span>','Fri<span>day</span>','Sat<span>urday</span>'),
			'lg' => array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),
			'md' => array('Sun','Mon','Tues','Wed','Thu','Fri','Sat'),
			'sm' => array('Su','Mo','Tu','We','Th','Fr','Sa')
		);
		
		if(!empty($this->headingSize) && array_key_exists($this->headingSize, $dotw)){ ?>
    
    	<tr>
				<th><div class="calendarDayName"><?= $dotw[$this->headingSize][0]; ?></div></th>
        <th><div class="calendarDayName"><?= $dotw[$this->headingSize][1]; ?></div></th>
				<th><div class="calendarDayName"><?= $dotw[$this->headingSize][2]; ?></div></th>
				<th><div class="calendarDayName"><?= $dotw[$this->headingSize][3]; ?></div></th>
				<th><div class="calendarDayName"><?= $dotw[$this->headingSize][4]; ?></div></th>
				<th><div class="calendarDayName"><?= $dotw[$this->headingSize][5]; ?></div></th>
				<th><div class="calendarDayName"><?= $dotw[$this->headingSize][6]; ?></div></th>
			</tr><? 
			
		} else {
			echo 'day name bork';
		}
		

	}
	 
	public function display($month, $year){
		
		$month_name = date('F', mktime(0, 0, 0, $month, 1, $year));
		$running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
		$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();
		
		ob_start(); ?>
		
    <table class="<?= $this->tableClass; ?>" data-month="<?= $month; ?>" data-year="<?= $year; ?>">
		
    <thead><?
		
		if($this->showMonthHeading){ ?>

      <tr>
        <th colspan="7" class="calendarMonth"><div class="calendarMonthName"><?= $month_name; ?></div></th>
      </tr><?
		
		}
		
		$this->getDayHeadings(); ?>
      
		</thead>
		<tbody>
    
    
		<tr><? 
    
    /* FIRST ROW - EMPTY DAYS*/
		for($x = 0; $x < $running_day; $x++){ ?>
			
			<td class="calendarEmpty"></td><? 
			
			$days_in_this_week++;
			
		}
	
		/* BEGIN DAYS */
		for($day = 1; $day <= $days_in_month; $day++){ 
		
			$populated = false;
			
			if(array_key_exists($day, $this->data)){ $populated = true;	} ?>
			
			<td class="calendarDay<? if($populated){ echo ' calendarActive';	} ?>">
      
      	<div id="calendar_<?= $year.$month.$day; ?>" >

					<div class="dayNumber">
          	<span class="calendarNumber"><?
						if($this->dayNumberLeadingZero && $day < 10){
							echo '0'.$day;
						} else {
							echo $day;
						} ?></span><span class="calendarSuffix"><?= date('S', strtotime($month.'/'.$day.'/'.$year)); ?></span>
          </div>
				
					<div class="dayContent"><? 
				
						if($populated){ ?>
            	<ul><?
              foreach($this->data[$day] as $item){ ?>
								<li><?= $item; ?></li><?
							} ?>
              </ul><?
            } ?>
            
          </div>
          
        </div>
				
			</td><? 
      
			if($running_day == 6){ ?>
			
				</tr><? 
				
				if(($day_counter+1) != $days_in_month){ ?>
				
					<tr><?
					
				}
				
				$running_day = -1;
				$days_in_this_week = 0;
				
			}
			
			$days_in_this_week++; 
			$running_day++; 
			$day_counter++;
			
		}
	
		/* TAIL EMPTY DAYS */
		
		if($days_in_this_week > 1){
		
			for($x = 1; $x <= (8 - $days_in_this_week); $x++){ ?>
			
				<td class="calendarEmpty"></td><? 
				
			}
			
		} ?>
    
    </tr>
    </tbody>
    </table><?  
		
		$content = ob_get_clean();
    
		return $content;
		
		
	} /* END display() */

} 