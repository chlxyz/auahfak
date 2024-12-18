<!-- Older IE warning message -->
    <!--[if lt IE 9]>
      <div class="ie-warning">
        <h1 class="c-white">IE SUCKS!</h1>
        <p>You are using an outdated version of Internet Explorer, upgrade to any of the following web browser <br/>in order to access the maximum functionality of this website. </p>
        <ul class="iew-download">
          <li>
            <a href="http://www.google.com/chrome/">
              <img src="img/browsers/chrome.png" alt="">
              <div>Chrome</div>
            </a>
          </li>
          <li>
            <a href="https://www.mozilla.org/en-US/firefox/new/">
              <img src="img/browsers/firefox.png" alt="">
              <div>Firefox</div>
            </a>
          </li>
          <li>
            <a href="http://www.opera.com">
              <img src="img/browsers/opera.png" alt="">
              <div>Opera</div>
            </a>
          </li>
          <li>
            <a href="https://www.apple.com/safari/">
              <img src="img/browsers/safari.png" alt="">
              <div>Safari</div>
            </a>
          </li>
          <li>
            <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
              <img src="img/browsers/ie.png" alt="">
              <div>IE (New)</div>
            </a>
          </li>
        </ul>
        <p>Upgrade your browser for a Safer and Faster web experience. <br/>Thank you for your patience...</p>
      </div>
    <![endif]-->
<?php
  $this->load->view('template/basic_bot');
?>

<script src="<?php echo base_url()?>assets/material/vendors/auto-size/jquery.autosize.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/material/vendors/nicescroll/jquery.nicescroll.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/material/vendors/waves/waves.min.js"></script>
<script src="<?php echo base_url(); ?>assets/material/vendors/moment/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/material/vendors/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/material/vendors/bootstrap-growl/bootstrap-growl.min.js"></script> -->
<script src="<?php echo base_url(); ?>assets/bootstrap-notif/bootstrap-notif.min.js"></script>
<script src="<?php echo base_url(); ?>assets/material/vendors/sweet-alert/sweet-alert.min.js"></script>
<script src="<?php echo base_url(); ?>assets/material/vendors/auto-size/jquery.autosize.min.js"></script>

<script src="<?php echo base_url(); ?>assets/material/vendors/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/material/vendors/summernote/summernote.min.js"></script>
<script src="<?php echo base_url(); ?>assets/material/js/functions.js"></script>
<script src="<?php echo base_url(); ?>assets/dataTables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/dataTables/js/dataTables.bootstrap.js"></script>

<!-- <script src="<?php echo base_url(); ?>assets/Bootstrap-DrillDownMenu/bootstrap.drilldown.js"></script>
<script src="<?php echo base_url(); ?>assets/Bootstrap-DrillDownMenu/jquery.cookie.js"></script>
<script src="<?php echo base_url(); ?>assets/js/runtime.js"></script> -->

<script type="text/javascript">
  var baseUrl = "<?php echo base_url();?>index.php";
  $('.dt').DataTable();
  function flyNotif() {
    $.ajax({
      url: baseUrl + 'ajax/notif_json',
      type: 'POST',
      dataType: 'json',
      data: {param1: 'value1'}
    })
    .done(function(respond) {
      $.each(respond,function(index, el) {
        if (el.count < 3) {
          type = 'info';
        } else if (el.count < 10) {
          type = 'warning';

        } else {
          type = 'danger';

        }
        $.notify({
          title: '<strong>'+el.title+'</strong><br />',
          message: el.message,
          url: el.url,
        },{
          type: type,
          delay:0,
          url_target: "_self"
        });

      });
    });

  }
</script>
<script type="text/javascript">

  $('.btn-lang').click(function(event) {
    event.preventDefault();
    var lang = $(this).data('lang');
    $.ajax({
      url: baseUrl + '/ajax/switch_lang',
      type: 'POST',
      data: {lang: lang}
    })
    .done(function() {
      location.reload();

    });

  });
  
  $(function(){
       
       $("#main > *").click(function(){
            $(".menudimas").hide();
       }); 
    });

</script>

<!--TAMBAHAN UNTUK LOADING TIAP KALI ADA AJAX-->
<style>
.ajax_loader {background: url("/js/ajax-loader/spinner_squares_circle.gif") no-repeat center center transparent;width:100%;height:100%;}
.blue-loader .ajax_loader {background: url("/js/ajax-loader/ajax-loader_blue.gif") no-repeat center center transparent;}

/*body {
  position:relative;
}*/
</style>
<script id="loader" src="/js/ajax-loader/script.js" type="text/javascript"></script>
<!-- <script>
$(function(){
	var box1;                

    $(document).ajaxStart(function() {
        box1 = new ajaxLoader($("body"), {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.5'});
//    		$("body").css('overflowY', 'hidden');
    }).ajaxStop(function(){
        box1.remove();
//        $("body").css('overflowY', 'scroll');
    });  
});
</script> -->
<!--TAMBAHAN AJAX DETECT OFF--> 
<script type="text/javascript">

  flyNotif();

</script>

<script type="text/babel">
var Mainmenu = React.createClass({
  getInitialState: function() {
    return {
      list_arr: [],
      paths:[],
      show: false
    };
  },
  handleClick: function(code) {
    this.refreshList(code);
    this.refreshPath(code);
  },
  handleToggle:function(){
    if (this.state.show) {
      this.setState({show:false});

    } else {
      this.setState({show:true});

    }
  },
  refreshList: function(param) {
    $.ajax({
      url: "<?php echo base_url();?>index.php/ajax/menu_branch",
      dataType: 'json',
      type: 'POST',
      data: { menu_code: param},
      success: function(data) {
        this.setState({list_arr: data});
      }.bind(this),
      error: function(xhr, status, err) {
        // this.setState({data: comments});
        console.error(this.props.url, status, err.toString());
      }.bind(this)
    });
  },
  refreshPath: function(param) {
    $.ajax({
      url: "<?php echo base_url();?>index.php/ajax/menu_breadcrumb",
      dataType: 'json',
      type: 'POST',
      data: { menu_code: param},
      success: function(data) {
        this.setState({paths: data});
      }.bind(this),
      error: function(xhr, status, err) {
        // this.setState({data: comments});
        console.error(this.props.url, status, err.toString());
      }.bind(this)
    });
  },
  componentDidMount: function() {
    this.refreshList('ROOT');
    this.refreshPath('ROOT');
  },
  render: function() {
    return (
      <div>
        <MainmenuBtn onClick={this.handleToggle} ></MainmenuBtn>
        {
            this.state.show ? <MainmenuPanel onClick={this.handleClick} list_arr={this.state.list_arr} paths={this.state.paths}></MainmenuPanel> :null
        }

      </div>
    );
  }
});

var MainmenuBtn = React.createClass({
  render: function(){
    return (
      <a className="btn btn-info waves-effect " onClick={this.props.onClick}>Menu</a>
    );
  }
});

var MainmenuPanel = React.createClass({
  render: function(){
    var click_url = this.props.onClick;
    var nodes = this.props.list_arr.map(function(item) {

      if (item.sub > 0) {
        return (
          <MainmenuLsItems code={item.code} icon={item.icon} onClick={click_url}>
          {item.label}
          </MainmenuLsItems>
        );

      } else {
        return (
          <MainmenuLsItem code={item.code} icon={item.icon} url={item.url}>
          {item.label}
          </MainmenuLsItem>
        );
      }
    });
    return (

      <div className=" card m-t-5 menudimas" >
        <div className=" card-body p-b-10">
          <MainmenuBc data={this.props.paths} onClick={this.props.onClick}></MainmenuBc>
          <div className="drilldown">
            <ul className="drilldown-menu nav p-l-0 p-r-0">
              {nodes}
            </ul>

          </div>
        </div>
      </div>
    );
  }
});

var MainmenuLsItems = React.createClass({

  render: function() {
    return (
      <li className="menu_item"  onClick={this.props.onClick.bind(this,this.props.code)}>
       <a className="waves-effect" >
       <i className={this.props.icon}></i> {this.props.children}
        <i className="fa fa-chevron-right pull-right"></i>
        </a>
      </li>
    );
  }
});

var MainmenuLsItem = React.createClass({
  render: function() {

    return (
      <li className="menu_item"><a className="waves-effect" href={this.props.url}> <i className={this.props.icon}></i>  {this.props.children}</a></li>
    );
  }
});

var MainmenuBc = React.createClass({
  render: function() {
    var click_url = this.props.onClick
    var nodes = this.props.data.map(function(item) {
      return (
        <MainmenuBcItem code={item.code} onClick={click_url.bind(this,item.code)}>
          {item.label}
        </MainmenuBcItem>
      );
    });
    return (
      <ol className="breadcrumb p-10 m-b-0 bgm-blue" >
        {nodes}
      </ol>
    );
  }
});

var MainmenuBcItem = React.createClass({
  render: function() {
    return (
      <li ><a class="waves-effect" href="#" className="c-white" onClick={this.props.onClick} >{this.props.children}</a></li>
    );
  }
});

ReactDOM.render(
  <Mainmenu />,
  document.getElementById('main-menu')
);

</script>

<script>

  var stage = new swiffy.Stage(document.getElementById('swiffycontainer'),
      swiffyobject, {});

  stage.start();
  
    
</script>
<?php

  $this->benchmark->mark('code_end');
  echo 'Load time:'.$this->benchmark->elapsed_time('code_start', 'code_end');
  echo '<br /> Used Memory:'.$this->benchmark->memory_usage('code_start', 'code_end');
?>
