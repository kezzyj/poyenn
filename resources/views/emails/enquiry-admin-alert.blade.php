<h2>New Enquiry Received</h2>
<p><strong>Product:</strong> {{ $product->name }}</p>
<p><strong>Customer:</strong> {{ $enquiry->customer_name }}</p>
<p><strong>Phone:</strong> {{ $enquiry->phone }}</p>
<p><strong>Email:</strong> {{ $enquiry->email ?: '—' }}</p>
<p><strong>Location:</strong> {{ $enquiry->location ?: '—' }}</p>
<p><strong>Message:</strong><br>{{ $enquiry->message ?: 'No message provided.' }}</p>
<p><a href="{{ route('admin.enquiries.show', $enquiry) }}">View in admin panel</a></p>