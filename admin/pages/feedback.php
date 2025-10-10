<?php
require_once __DIR__ .'/../../config/dbconfig.php';

$database = new Database();
$conn = $database->dbConnection();

$conn->exec("UPDATE contact_message SET is_read = 1 WHERE is_read = 0");

$sql = "SELECT cm.*, 
		CASE WHEN u.id IS NULL THEN 0 ELSE 1 END AS is_registered
		FROM contact_message cm
		LEFT JOIN users u ON u.email = cm.email
		ORDER BY cm.created_at DESC";

$stmt = $conn->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
	<h4 class="mb-0">user Feedback</h4>
	<div>
		<span class="badge badge-info"> Total: <?= count($rows) ?></span>
	<button id="deleteSelected" class="btn btn-sm btn-danger ml-2" disabled>Delete Selected</button>
	</div>
	
</div>

<div class="table-responsive">
	<table class="table table-bordered table-striped" id="fbTable">
		<thead>
			<tr>
				<th style="width: 40px;">Select Message
					<input type="checkbox" name="selectAll" id="selectAll">
				</th>
				<th>#</th>
				<th>Name / Email</th>
				<th>Subject</th>
				<th>Message</th>
				<th>Received</th>
				<th>Status</th>
				<th>Reply</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rows as $i => $r):?>				
			<tr data-id="<?= (int)$r['id'];?>">
				<td>
					<input type="checkbox" name="rowChk" class="rowChk" value="<?= (int)$r['id'] ?>">
				</td>
				<td><?= $i + 1 ?></td>
				<td>
					<div><strong><?= htmlspecialchars($r['name']) ?></strong><?php if((int)$r['is_registered'] === 0):?>
						<small class="text-muted">(Not Registered)</small>
					<?php endif; ?>
					</div>
					<div class="text-muted small"><?= htmlspecialchars($r['email']) ?></div>
				</td>
				<td><?= htmlspecialchars($r['subject'] ? : '--')?></td>

				<td style="max-width: 360px; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($r['message'])) ?></td>
				<td class="small"><?= htmlspecialchars($r['created_at'])?></td>
				<td>
					<?php if((int)$r['is_replied'] === 1):?>
						<span class="badge badge-success">Replies</span>
						<?php else: ?>
							<span class="badge badge-warning">Pending</span>
						<?php endif; ?>
				</td>

				<td style="min-width: 240px;">
					<?php if((int)$r['is_replied'] === 0):?>
						<button class="btn btn-sm btn-primary relpyBtn" data-id="<?= $r['id']?>">Reply</button>
						<div class="replyBox mt-2 d-none" id="rb-<?= $r['id']?>">
							<textarea name="" class="form-control mb-2" rows="3" placeholder="Type your reply..." id="rt-<?= $r['id'] ?>" ></textarea>
							<button class="btn btn-sm btn-success sendReply" data-id="<?= $r['id'] ?>">Send</button>
							<div class="small text-muted mt-1">This will be emailed to the visitor.</div>
						</div>
						<?php else: ?>
							<div class="small text-muted">
								Sent at: <?= htmlspecialchars($r['replied_at'] ? : '--')?>
							</div>
						<?php
							if(!empty($r['reply_text'])):?>
								<details class="mt-1">
									<summary>View Reply</summary>
									<div class="small" style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($r['reply_text'])) ?></div>	
								</details>
							<?php endif; ?>
						<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

<script type="text/javascript">

	document.addEventListener('click',  function(e)
	{

		if(e.target.classList.contains('relpyBtn'))
		{
			var id = e.target.getAttribute('data-id');
			document.getElementById('rb-'+id).classList.toggle('d-none');
		}

		if(e.target.classList.contains('sendReply'))
		{
			var id = e.target.getAttribute('data-id');
			var txt = (document.getElementById('rt-'+id).value || '').trim();

			if(!txt)
			{
				alert('Please type your reply');
				return;
			}

			fetch('ajax/send_reply.php', {

				method: 'POST',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				credentials: 'same-origin',
				body: 'id='+encodeURIComponent(id)+'&reply='+encodeURIComponent(txt)
			}).then(r => r.json()).then(d => {

				if(d.ok) { alert('Reply Sent!'); loaction.reload();}

				else { alert(d.err || 'Failed to send');}
			}).catch(()=>alert('Unexpected Error'));
		}


	});

	(function()
	{

		var selectAll = document.getElementById('selectAll');
		var table = document.getElementById('fbTable');
		var deleteBtn = document.getElementById('deleteSelected');

		function updateDeleteBtnState()
		{
			var anyChecked = table.querySelectorAll('tbody .rowChk:checked').length > 0;
			deleteBtn.disabled =  !anyChecked; 
		}

		if(selectAll)
		{
			selectAll.addEventListener('change', function(){

				var rows = table.querySelectorAll('tbody .rowChk');
				rows.forEach(function(chk)
				{

					chk.checked = selectAll.checked;
				});
				updateDeleteBtnState();
			});
		}

		table.addEventListener('change', function(e)
		{

			if(e.target.classList.contains('rowChk'))
			{
				var all = table.querySelectorAll('tbody .rowChk').length;
				var sel = table.querySelectorAll('tbody .rowChk:checked').length;
				if(selectAll) selectAll.checked = (all > 0 && sel === all);
				updateDeleteBtnState();
			}
		});

		deleteBtn.addEventListener('click', function(){

			var ids = Array.from(table.querySelectorAll('tbody .rowChk:checked')).map(function(chk){

				return chk.value;
			});

			if(ids.length === 0)

			{
				return;
			}

			if(!confirm('Delete selected '+ids.length+ 'message(s)? This action cannot be undone.'))
			{
				return;
			}

			fetch('ajax/delete_feedeback.php',
			{
				method: 'POST',
				headers: {'Content-Type': 'application/json'},
				creadentials: 'same-origin',
				body: JSON.stringify({ids: ids})

			}).then(r => r.json()).then(d => {
				if(d.ok)
				{
					alert("Deleted: "+d.deleted+ ' message(s).');
					location.reload();
				}

				else
				{
					alert(d.error || 'Delete failed');
				}
			}).catch(() => alert('Unexpected Error'));

		});
	})();	

			
</script>