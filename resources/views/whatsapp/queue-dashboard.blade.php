@extends('layouts.app')

@section('title', 'WhatsApp Queue Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fab fa-whatsapp text-success"></i>
                        WhatsApp Queue Dashboard
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="refreshStats()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="processQueue()">
                            <i class="fas fa-play"></i> Process Queue
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3 id="pending-count">-</h3>
                                    <p>Pending Messages</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3 id="sent-count">-</h3>
                                    <p>Sent Messages</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3 id="failed-count">-</h3>
                                    <p>Failed Messages</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3 id="processing-count">-</h3>
                                    <p>Processing</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-spinner"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter and Actions -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status-filter">Filter by Status:</label>
                                <select id="status-filter" class="form-control" onchange="loadQueueList()">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="sent">Sent</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="button" class="btn btn-warning" onclick="retryFailedMessages()">
                                    <i class="fas fa-redo"></i> Retry Failed
                                </button>
                                <button type="button" class="btn btn-danger" onclick="clearSentMessages()">
                                    <i class="fas fa-trash"></i> Clear Sent
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Queue List Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="queue-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Phone Number</th>
                                    <th>Message</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date/Time</th>
                                    <th>Source</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="queue-tbody">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div id="queue-info"></div>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Queue pagination">
                                <ul class="pagination justify-content-end" id="queue-pagination">
                                    <!-- Pagination will be loaded here -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Detail Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="message-details">
                <!-- Message details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentStatus = '';

$(document).ready(function() {
    refreshStats();
    loadQueueList();
    
    // Auto refresh every 30 seconds
    setInterval(function() {
        refreshStats();
        loadQueueList();
    }, 30000);
});

function refreshStats() {
    $.get('{{ route("ilp.whatsapp.queue.stats") }}')
        .done(function(data) {
            if (data.success) {
                $('#pending-count').text(data.stats.pending || 0);
                $('#sent-count').text(data.stats.sent || 0);
                $('#failed-count').text(data.stats.failed || 0);
                $('#processing-count').text(data.stats.processing || 0);
            }
        })
        .fail(function() {
            console.error('Failed to load statistics');
        });
}

function loadQueueList(page = 1) {
    currentPage = page;
    currentStatus = $('#status-filter').val();
    
    let url = '{{ route("ilp.whatsapp.queue.list") }}?page=' + page;
    if (currentStatus) {
        url += '&status=' + currentStatus;
    }
    
    $('#queue-tbody').html('<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');
    
    $.get(url)
        .done(function(data) {
            if (data.success) {
                renderQueueTable(data.messages);
                renderPagination(data.pagination);
            } else {
                $('#queue-tbody').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
            }
        })
        .fail(function() {
            $('#queue-tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load queue list</td></tr>');
        });
}

function renderQueueTable(messages) {
    let html = '';
    
    if (messages.length === 0) {
        html = '<tr><td colspan="8" class="text-center">No messages found</td></tr>';
    } else {
        messages.forEach(function(message) {
            let statusBadge = getStatusBadge(message.status);
            let typeBadge = getTypeBadge(message.type);
            let truncatedMessage = message.pesan.length > 50 ? 
                message.pesan.substring(0, 50) + '...' : message.pesan;
            
            html += `
                <tr>
                    <td>${message.nomor}</td>
                    <td>${message.nowa}</td>
                    <td title="${message.pesan}">${truncatedMessage}</td>
                    <td>${typeBadge}</td>
                    <td>${statusBadge}</td>
                    <td>${formatDateTime(message.tanggal_jam)}</td>
                    <td>${message.source || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="showMessageDetails(${message.nomor})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${message.status === 'failed' ? 
                            `<button class="btn btn-sm btn-warning" onclick="retryMessage(${message.nomor})">
                                <i class="fas fa-redo"></i>
                            </button>` : ''}
                        ${message.status !== 'processing' ? 
                            `<button class="btn btn-sm btn-danger" onclick="deleteMessage(${message.nomor})">
                                <i class="fas fa-trash"></i>
                            </button>` : ''}
                    </td>
                </tr>
            `;
        });
    }
    
    $('#queue-tbody').html(html);
}

function renderPagination(pagination) {
    let html = '';
    
    if (pagination.last_page > 1) {
        // Previous button
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" onclick="loadQueueList(${pagination.current_page - 1})">
                            Previous
                        </a>
                    </li>`;
        }
        
        // Page numbers
        for (let i = Math.max(1, pagination.current_page - 2); 
             i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadQueueList(${i})">${i}</a>
                    </li>`;
        }
        
        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" onclick="loadQueueList(${pagination.current_page + 1})">
                            Next
                        </a>
                    </li>`;
        }
    }
    
    $('#queue-pagination').html(html);
    
    // Update info
    $('#queue-info').html(`Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} entries`);
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge badge-info">Pending</span>',
        'processing': '<span class="badge badge-warning">Processing</span>',
        'sent': '<span class="badge badge-success">Sent</span>',
        'failed': '<span class="badge badge-danger">Failed</span>'
    };
    return badges[status] || '<span class="badge badge-secondary">Unknown</span>';
}

function getTypeBadge(type) {
    const badges = {
        'text': '<span class="badge badge-primary">Text</span>',
        'document': '<span class="badge badge-info">Document</span>',
        'template': '<span class="badge badge-success">Template</span>'
    };
    return badges[type] || '<span class="badge badge-secondary">Unknown</span>';
}

function formatDateTime(datetime) {
    return new Date(datetime).toLocaleString('id-ID');
}

function processQueue() {
    if (!confirm('Process pending messages in queue?')) return;
    
    $.post('{{ route("ilp.whatsapp.queue.process") }}')
        .done(function(data) {
            if (data.success) {
                alert('Queue processing started successfully!');
                refreshStats();
                loadQueueList();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .fail(function() {
            alert('Failed to start queue processing');
        });
}

function retryMessage(id) {
    if (!confirm('Retry sending this message?')) return;
    
    $.post(`{{ url('ilp/whatsapp/queue') }}/${id}/retry`)
        .done(function(data) {
            if (data.success) {
                alert('Message retry initiated!');
                refreshStats();
                loadQueueList();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .fail(function() {
            alert('Failed to retry message');
        });
}

function deleteMessage(id) {
    if (!confirm('Delete this message from queue?')) return;
    
    $.ajax({
        url: `{{ url('ilp/whatsapp/queue') }}/${id}`,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(data) {
        if (data.success) {
            alert('Message deleted successfully!');
            refreshStats();
            loadQueueList();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .fail(function() {
        alert('Failed to delete message');
    });
}

function retryFailedMessages() {
    if (!confirm('Retry all failed messages?')) return;
    
    // This would trigger the command to retry failed messages
    alert('Retry failed messages feature will be implemented via command line or scheduled task.');
}

function clearSentMessages() {
    if (!confirm('Clear all sent messages from queue? This action cannot be undone.')) return;
    
    // Implementation for clearing sent messages
    alert('Clear sent messages feature can be implemented as needed.');
}

function showMessageDetails(id) {
    // Load and show message details in modal
    $('#message-details').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
    $('#messageModal').modal('show');
    
    // This would load detailed message information
    setTimeout(function() {
        $('#message-details').html('<p>Message details would be loaded here...</p>');
    }, 1000);
}
</script>
@endpush

@push('styles')
<style>
.small-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    display: block;
    margin-bottom: 20px;
    position: relative;
}

.small-box > .inner {
    padding: 10px;
}

.small-box > .small-box-footer {
    background: rgba(0,0,0,.1);
    color: rgba(255,255,255,.8);
    display: block;
    padding: 3px 0;
    position: relative;
    text-align: center;
    text-decoration: none;
    z-index: 10;
}

.small-box > .icon {
    color: rgba(255,255,255,.15);
    z-index: 0;
}

.small-box > .icon > i {
    font-size: 70px;
    position: absolute;
    right: 15px;
    top: 15px;
    transition: transform .3s linear;
}

.small-box:hover {
    text-decoration: none;
    color: #fff;
}

.small-box:hover > .icon > i {
    transform: scale(1.1);
}

.bg-info {
    background-color: #17a2b8!important;
    color: #fff;
}

.bg-success {
    background-color: #28a745!important;
    color: #fff;
}

.bg-danger {
    background-color: #dc3545!important;
    color: #fff;
}

.bg-warning {
    background-color: #ffc107!important;
    color: #212529;
}
</style>
@endpush