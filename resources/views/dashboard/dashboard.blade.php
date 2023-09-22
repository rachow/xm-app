@php
    $title = 'Forex & Trading OHLCV Charts';
    $body_class = 'xm-app';
    $config = [
        'app-id'    => '9b4d05a7-2a5e-4fd3-b2a1-3316e918943f',
        'api-url'   => '/api/v1',
        'api-doc'   => '/api/docs',
    ];

@endphp
@extends ('layouts.app')
@section ('content')
<div class="jumbotron">
    <div class="container">
        <h1 class="display-3">Hey Trader!</h1>
        <p class="lead">Analyse and leap the market using our historical OHLCV charts.</p>
    </div>
</div>
<div class="container pb-3">
    @include ('partials.forms.chart_form')
    <div id="xm-chart" class="mt-3" style="min-height: 300px; width: 100%; text-align: center;">
        Loading...
    </div>
</div>
@endsection
@push ('bottom-script')
<script type="text/javascript">
/**
 * Mudularize JS
*/
const ipdata_url = 'https://api.ipdata.co';
const api_key = 'ab2bada3bfc05a4daef10ab3845407ae04ea57bf9ee2de3f5bcb5bf5';

window.addEventListener('load', () => {
    window.config = JSON.parse(decodeURIComponent(escape(window.atob('<?php echo base64_encode(json_encode($config)); ?>'))));
    log(config);
});

$(function(){

	let css = log_styles + ';font-size:2em;padding:10px;';
    logConsole('[ / ] loading...', css, rocket);

    // Fetching country information
    $.get(ipdata_url + '?api-key=' + api_key, function (response) {
        $("input[name=ipdata]").val(encodeURIComponent(JSON.stringify(response)));
    }, "jsonp");



	$('#btnGetCharts').on('click', function(e){
        e.preventDefault();

        /**
         * plugin jquery.validate could be used in future.
        */

        let symbol = $('#symbol').val();
        let startdate = $('#startdate').val();
        let enddate = $('#enddate').val();
        let email = $('#email').val();
        let err = false;
        const date_expr = new RegExp("/^\d{4}-\d{2}-\d{2}$/");
        const email_expr = new RegExp("/^[A-Z0-9][A-Z0-9._%+-]{0,63}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/");

        $('.error').remove(); // clear = reset
        
        if (symbol.length < 3) {
            $('#symbol').after('<span class="error">You must select a symbol</span>');
            err = true;
        }

        if (startdate == "" || !startdate.test(date_expr)) {
            $('#startdate').after('<span class="error">date must be yyyy-mm-dd</span>');
            err = true;
        }

        if (enddate == "" || !enddate.test(date_expr)) {
            $('#enddate').after('<span class="error">date must be yyyy-mm-dd</span>');
            err = true;
        }

        if (email.length == 0 || email == "" || !email.test(email_expr)) {
            $('#email').after('<span class="error">email is invalid</span>');
            err = true;
        }

        if (!(err == true)) {
            btnLoadingState($(this).attr('id'));
            
        }

        let qryChart = getSymbolChartData('xm-symbol-fm'); 
        qryChart.then( resp => {
            log('receiving the chart data');
            log(resp.data);
        }).catch(err => {
            log(err);
        });

	});

    let el_sdate = '#startdate';
    let el_edate = '#enddate';

    // grab instant API access
    let sdate = new AirDatepicker(el_sdate);
    let edate = new AirDatepicker(el_edate);

    let symbols = getSymbols();
    symbols.then(resp => {
        log('receiving the symbols');
        log(resp.data);
        let el = '#xm-symbols';
        buildSymbolsOptions(el, resp.data);
    }
    ).catch(err => {
        log(err);
    });
   
    $(el_sdate).on('blur', function(){
        let d = sdate.selectedDates;
        if (d && d != undefined) {
            log(d[0]);
        }
    });

});

const getSymbols = async (data) => {
    /**
    *   1. We can cache the data on the device
    *       (a) is storage available (local vs session)
    *       (b) store the data and only fetch on following terms
    *           (i)  initial load made by fetch to backend.
    *           (ii) data is terminated on browser session closed 
    */

    let url = '/markets/symbols?'+getEpoch();
    const result = await axios.post(url, data)
    return result.data;
};

const getSymbolChartData = async (fm) => {
    let url = '/markets/symbol/history';
    let data = $('#'+fm).serialize();
    const result = await axios.post(url, data);
    return result.data;
};

const buildSymbolsOptions = (el, data) => {
    if (Array.isArray(data)) {
        let options = '<select name="symbols" id="symbols" class="form-control">';
        let label = '<label>Company symbol</label>';
        $.each(data, (idx, obj) => {
            options += '<option value="'+obj.Symbol+'">'+obj.Symbol+'</option>';
        });
        options += '</select>';
        $(el).html(label + options);
    }
};

const displaySymbolChartData = (data) => {
    // here we populate the chart
    // https://github.com/chartjs/chartjs-chart-financial/

    let b_count = 60;
    let init_date_str = '19 Sep 2023 00:00 Z';
    let ct = $('#xm-chart'); // document.querySelector('#xm-chart');
    ct.canvas.width = '100%';
    ct.canvas.height = 300;

    let bar_data = '';
};


const randomBar = (date, lastClose) => {
	var open = +randomNumber(lastClose * 0.95, lastClose * 1.05).toFixed(2);
	var close = +randomNumber(open * 0.95, open * 1.05).toFixed(2);
	var high = +randomNumber(Math.max(open, close), Math.max(open, close) * 1.1).toFixed(2);
	var low = +randomNumber(Math.min(open, close) * 0.9, Math.min(open, close)).toFixed(2);
	return {
		x: date.valueOf(),
		o: open,
		h: high,
		l: low,
		c: close
	};
};

const getRandomData = (dateStr, count) => {
	var date = luxon.DateTime.fromRFC2822(dateStr);
	var data = [randomBar(date, 30)];
	while (data.length < count) {
		date = date.plus({days: 1});
		if (date.weekday <= 5) {
			data.push(randomBar(date, data[data.length - 1].c));
		}
	}
	return data;
};


let update = function() {
	var dataset = chart.config.data.datasets[0];

	// candlestick vs ohlc
	var type = document.getElementById('type').value;
	dataset.type = type;

	// linear vs log
	var scaleType = document.getElementById('scale-type').value;
	chart.config.options.scales.y.type = scaleType;

	// color
	var colorScheme = document.getElementById('color-scheme').value;
	if (colorScheme === 'neon') {
		dataset.color = {
			up: '#01ff01',
			down: '#fe0000',
			unchanged: '#999',
		};
	} else {
		delete dataset.color;
	}

	// border
	var border = document.getElementById('border').value;
	var defaultOpts = Chart.defaults.elements[type];
	if (border === 'true') {
		dataset.borderColor = defaultOpts.borderColor;
	} else {
		dataset.borderColor = {
			up: defaultOpts.color.up,
			down: defaultOpts.color.down,
			unchanged: defaultOpts.color.up
		};
	}

	// mixed charts
	var mixed = document.getElementById('mixed').value;
	if(mixed === 'true') {
		chart.config.data.datasets = [
			{
				label: 'CHRT - Chart.js Corporation',
				data: barData
			},
			{
				label: 'Close price',
				type: 'line',
				data: lineData()
			}	
		]
	}
	else {
		chart.config.data.datasets = [
			{
				label: 'CHRT - Chart.js Corporation',
				data: barData
			}	
		]
	}

	chart.update();
};


var barData = getRandomData(initialDateStr, barCount);
function lineData() { return barData.map(d => { return { x: d.x, y: d.c} }) };

var chart = new Chart(ctx, {
	type: 'candlestick',
	data: {
		datasets: [{
			label: 'CHRT - Chart.js Corporation',
			data: barData
		}]
	}
});

var getRandomInt = function(max) {
	return Math.floor(Math.random() * Math.floor(max));
};

function randomNumber(min, max) {
	return Math.random() * (max - min) + min;
}

</script>
@endpush

