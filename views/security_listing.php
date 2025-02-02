<?php $this->view('partials/head'); ?>

<div class="container">
  <div class="row">
  	<div class="col-lg-12">
		  <h3><span data-i18n="security.report"></span> <span id="total-count" class='label label-primary'>…</span></h3>
		  <table class="table table-striped table-condensed table-bordered">
		    <thead>
		      <tr>
		        <th data-i18n="listing.computername" data-colname='machine.computer_name'></th>
		        <th data-i18n="serial" data-colname='reportdata.serial_number'></th>
		        <th data-i18n="username" data-colname='reportdata.long_username'></th>
		        <th data-i18n="filevault_status.users" data-colname='filevault_status.filevault_users'></th>
		        <th data-i18n="type"data-colname='machine.machine_name'></th>
		        <th data-i18n="disk_report.encryption_status" data-colname='diskreport.encrypted'></th>
		        <th data-i18n="security.gatekeeper" data-colname='security.gatekeeper'></th>
		        <th data-i18n="security.sip" data-colname='security.sip'></th>
		        <th data-i18n="security.firmwarepw" data-colname='security.firmwarepw'></th>
		        <th data-i18n="security.firewall_state" data-colname='security.firewall_state'></th>
		        <th data-i18n="security.skel.kext-loading" data-colname='security.skel_state'></th>
		        <th data-i18n="security.ssh_groups" data-colname='security.ssh_groups'></th>
		        <th data-i18n="security.ssh_users" data-colname='security.ssh_users'></th>
		        <th data-i18n="security.ard_users" data-colname='security.ard_users'></th>
		        <th data-i18n="security.ard_groups" data-colname='security.ard_groups'></th>
		        <th data-i18n="security.root_user" data-colname='security.root_user'></th>
		        <th data-i18n="security.t2_secureboot" data-colname='security.t2_secureboot'></th>
		        <th data-i18n="security.t2_externalboot" data-colname='security.t2_externalboot'></th>
		      </tr>
		    </thead>
		    <tbody>
		    	<tr>
		    	  <td data-i18n="listing.loading" colspan="18" class="dataTables_empty"></td>
		    	</tr>
		    </tbody>
		  </table>
    </div> <!-- /span 12 -->
  </div> <!-- /row -->
</div>  <!-- /container -->

<script type="text/javascript">

	$(document).on('appUpdate', function(e){

		var oTable = $('.table').DataTable();
		oTable.ajax.reload();
		return;

	});

	$(document).on('appReady', function(e, lang) {

        // Get modifiers from data attribute
        var mySort = [], // Initial sort
            hideThese = [], // Hidden columns
            col = 0, // Column counter
            runtypes = [], // Array for runtype column
            columnDefs = [{ visible: false, targets: hideThese }]; //Column Definitions

        $('.table th').map(function(){

            columnDefs.push({name: $(this).data('colname'), targets: col, render: $.fn.dataTable.render.text()});

            if($(this).data('sort')){
              mySort.push([col, $(this).data('sort')])
            }

            if($(this).data('hide')){
              hideThese.push(col);
            }

            col++
        });

	    oTable = $('.table').dataTable( {
            ajax: {
                url: appUrl + '/datatables/data',
                type: "POST",
                data: function(d){

                    // Look for a encrypted statement
                    if(d.search.value.match(/^encrypted = \d$/))
                    {
                        // Add column specific search
                        d.columns[6].search.value = d.search.value.replace(/.*(\d)$/, '= $1');
                        // Clear global search
                        d.search.value = '';
                    }

                    if(d.search.value.match(/^firewall = \d$/))
                    {
                        // Add column specific search
                        d.columns[10].search.value = d.search.value.replace(/.*(\d)$/, '= $1');
                        // Clear global search
                        d.search.value = '';
                    }

                    // Only search on bootvolume
                    d.where = [
                        {
                            table: 'diskreport',
                            column: 'mountpoint',
                            value: '/'
                        }
                    ];

                }
            },
            dom: mr.dt.buttonDom,
            buttons: mr.dt.buttons,
            order: mySort,
            columnDefs: columnDefs,
		    createdRow: function( nRow, aData, iDataIndex ) {
	        	// Update name in first column to link
	        	var name=$('td:eq(0)', nRow).html();
	        	if(name == ''){name = "No Name"};
	        	var sn=$('td:eq(1)', nRow).html();
	        	var link = mr.getClientDetailLink(name, sn);
	        	$('td:eq(0)', nRow).html(link);

                var enc = $('td:eq(5)', nRow).html();
                $('td:eq(5)', nRow).html(function(){
                    if( enc == 1){
                        return '<span class="label label-success">'+i18n.t('encrypted')+'</span>';
                    }
                    return '<span class="label label-danger">'+i18n.t('unencrypted')+'</span>';
                });

                var gk = $('td:eq(6)', nRow).html();
                $('td:eq(6)', nRow).html(function(){
                  if( gk == 'Active'){
                        return '<span class="label label-success">'+i18n.t('enabled')+'</span>';
                    } else if( gk == 'Not Supported'){
                        return '<span class="label label-warning">'+i18n.t('unsupported')+'</span>';
                    } else {
                        return '<span class="label label-danger">'+i18n.t('disabled')+'</span>';
                    }
                });

                var sip = $('td:eq(7)', nRow).html();
                $('td:eq(7)', nRow).html(function(){
                    if( sip == 'Active'){
                        return '<span class="label label-success">'+i18n.t('enabled')+'</span>';
                    } else if( sip == 'Not Supported'){
                        return '<span class="label label-warning">'+i18n.t('unsupported')+'</span>';
                    } else {
                        return '<span class="label label-danger">'+i18n.t('disabled')+'</span>';
                    }
                });

                 var firmwarepw = $('td:eq(8)', nRow).html();
                 $('td:eq(8)', nRow).html(function(){
                     if( firmwarepw == 'Yes' || firmwarepw == 'command'){
                         return '<span class="label label-success">'+i18n.t('enabled')+'</span>';
                     } else if ( firmwarepw == 'No'){
                         return '<span class="label label-danger">'+i18n.t('disabled')+'</span>';
                     } else if ( firmwarepw == 'Not Supported'){
                     return '<span class="label label-info">'+i18n.t('unsupported')+'</span>';
                     }
                     return '<span class="label label-default">'+i18n.t('unknown')+'</span>';
                 });

                var firewall_state = $('td:eq(9)', nRow).html();
                $('td:eq(9)', nRow).html(function(){
                   if(firewall_state == '1'){
                       return '<span class="label label-success">'+i18n.t('enabled')+'</span>';
                   } else if (firewall_state == '2'){
                        return '<span class="label label-success">'+i18n.t('security.block_all')+'</span>';
                   } else if (firewall_state == '0'){
                        return '<span class="label label-danger">'+i18n.t('disabled')+'</span>';
                   }
                   // default case - return blank if client has not checked in with this info
                   return "";
                });

                var skel_state = $('td:eq(10)', nRow).html();
                $('td:eq(10)', nRow).html(function(){
                    if(skel_state == '1'){
                        return '<span class="label label-info">'+i18n.t('security.skel.all-approved')+'</span>';
                    } else if (skel_state == '0'){
                        return '<span class="label label-info">'+i18n.t('security.skel.user-approved')+'</span>';
                    }
                    // if skel_state is null, we don't have data
                    return '<span class="label label-default">'+i18n.t('unknown')+'</span>';
                });

                var root_user = $('td:eq(15)', nRow).html();
                $('td:eq(15)', nRow).html(function(){
                    if(root_user == '1'){
                        return '<span class="label label-danger">'+i18n.t('enabled')+'</span>';
                    } else if (root_user == '0'){
                        return '<span class="label label-success">'+i18n.t('disabled')+'</span>';
                    }
                    // if root_user is null, we don't have data
                    return '<span class="label label-default">'+i18n.t('unknown')+'</span>';
                });

                var secure_boot = $('td:eq(16)', nRow).html();
                $('td:eq(16)', nRow).html(function(){
                    if(secure_boot == 'SECUREBOOT_FULL'){
                        return '<span class="label label-success">'+i18n.t('security.full')+'</span>';
                    } else if (secure_boot == 'SECUREBOOT_MEDIUM'){
                        return '<span class="label label-warning">'+i18n.t('security.medium')+'</span>';
                    } else if (secure_boot == 'SECUREBOOT_OFF'){
                        return '<span class="label label-danger">'+i18n.t('security.off')+'</span>';
                    } else if (secure_boot == 'SECUREBOOT_UNSUPPORTED'){
                        return '<span class="label label-info">'+i18n.t('security.unsupported')+'</span>';
                    }

                    // if root_user is null, we don't have data
                    return '<span class="label label-default">'+i18n.t('unknown')+'</span>';
                });

                var external_boot = $('td:eq(17)', nRow).html();
                $('td:eq(17)', nRow).html(function(){
                    if(external_boot == 'EXTERNALBOOT_ON'){
                        return '<span class="label label-danger">'+i18n.t('security.on')+'</span>';
                    } else if (external_boot == 'EXTERNALBOOT_OFF'){
                        return '<span class="label label-success">'+i18n.t('security.off')+'</span>';
                    } else if (external_boot == 'EXTERNALBOOT_UNSUPPORTED'){
                        return '<span class="label label-info">'+i18n.t('security.unsupported')+'</span>';
                    }
                    // if root_user is null, we don't have data
                    return '<span class="label label-default">'+i18n.t('unknown')+'</span>';
                });
            }
        });
    });
</script>

<?php $this->view('partials/foot'); ?>
