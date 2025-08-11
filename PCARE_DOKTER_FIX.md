# PCare Dokter Endpoint Fix Documentation

## Problem Description

The PCare API `dokter` endpoint was returning HTML error pages with "Request Error" instead of JSON responses. The logs showed:

```
Request Error
Status: 400
Content-Type: text/html
```

## BPJS Catalog Specification

According to the official BPJS PCare catalog:

**Endpoint Format:**
```
{Base URL}/{Service Name}/dokter/{Parameter 1}/{Parameter 2}
```

**Specifications:**
- **Function**: Get Data Dokter
- **Method**: GET
- **Format**: JSON
- **Content-Type**: `application/json; charset=utf-8`
- **Parameter 1**: Row data awal yang akan ditampilkan (starting row)
- **Parameter 2**: Limit jumlah data yang akan ditampilkan (data limit)

**Expected Response:**
```json
{
  "response": {
    "count": 19,
    "list": [
      {
        "kdDokter": "001",
        "nmDokter": "dr..."
      }
    ]
  },
  "metaData": {
    "message": "OK",
    "code": 200
  }
}
```

## Root Causes

1. **Endpoint format mismatch**: Not following the exact `dokter/{start}/{limit}` format required by BPJS
2. **Missing endpoint normalization**: No specific handling for dokter endpoint format validation
3. **No retry mechanism**: When HTML errors occurred, there was no fallback strategy with alternative formats

## Implemented Fixes

### 1. Endpoint Format Normalization

**File**: `app/Traits/PcareTrait.php`

**Before:**
```php
// Generic handling for all endpoints
if ($method === 'GET') {
    $endpoint .= '?offset=0&limit=10';
}
```

**After:**
```php
// Specific handling for dokter endpoint
elseif (strpos($endpoint, 'dokter') !== false) {
    // Format: {Base URL}/{Service Name}/dokter/{Parameter 1}/{Parameter 2}
    if (!preg_match('/dokter\/(\d+)\/(\d+)/', $endpoint)) {
        if ($endpoint === 'dokter') {
            $endpoint = 'dokter/0/100';
        }
    }
}
```

### 2. Content-Type Header Compliance

**Before:**
```php
// Inconsistent content type handling
if (strpos($endpoint, 'dokter') !== false) {
    $headers['Content-Type'] = 'application/json';
}
```

**After:**
```php
// BPJS catalog compliant content type
elseif (strpos($endpoint, 'dokter') !== false) {
    // Sesuai katalog BPJS: application/json; charset=utf-8
    $headers['Content-Type'] = 'application/json; charset=utf-8';
}
```

### 3. Enhanced Error Handling with Retry Mechanism

**Added comprehensive retry logic:**
```php
// Khusus untuk endpoint dokter, coba retry dengan format alternatif jika error
if (strpos($endpoint, 'dokter') !== false && $statusCode === 400) {
    $alternativeEndpoints = [];
    
    if (preg_match('/dokter\/(\d+)\/(\d+)/', $endpoint, $matches)) {
        $start = intval($matches[1]);
        $limit = intval($matches[2]);
        
        if ($start === 0) {
            $alternativeEndpoints[] = "dokter/1/{$limit}";
        }
        $alternativeEndpoints[] = 'dokter';
        $alternativeEndpoints[] = 'ref/dokter';
    }
    
    // Try each alternative endpoint
    foreach ($alternativeEndpoints as $altEndpoint) {
        // Retry logic with proper headers
    }
}
```

### 4. HTTP Client Configuration

**Simplified and standardized:**
```php
// Consistent HTTP client setup for all endpoints
$httpClient = Http::timeout(30)->withHeaders($headers);
```

## Key Improvements

1. **BPJS Catalog Compliance**: Follows exact specification from official BPJS documentation
2. **Automatic Format Validation**: Ensures `dokter/{start}/{limit}` format
3. **Intelligent Retry**: Attempts multiple endpoint formats when primary fails
4. **Proper Content-Type**: Uses `application/json; charset=utf-8` as specified
5. **Enhanced Logging**: Better debugging information for troubleshooting

## Testing Procedures

### 1. Basic Dokter Endpoint Test
```php
// Test default format
$result = $this->requestPcare('dokter');
// Should auto-convert to 'dokter/0/100'
```

### 2. Pagination Test
```php
// Test with specific pagination
$result = $this->requestPcare('dokter/0/50');
$result = $this->requestPcare('dokter/1/100');
```

### 3. Error Recovery Test
```php
// Simulate error to test retry mechanism
// Should automatically try alternative formats
```

## Monitoring Guidelines

### Log Patterns to Monitor

1. **Success Pattern:**
```
PCare API Response: status=200
Alternative dokter endpoint response: status=200
```

2. **Retry Pattern:**
```
Dokter endpoint error, attempting retry with alternative formats
Trying alternative dokter endpoint: dokter/1/100
```

3. **Error Pattern:**
```
Alternative dokter endpoint failed: endpoint=ref/dokter
```

### Performance Metrics
- Response time for dokter endpoints
- Success rate before/after retry
- Cache hit rate for dokter data

## Future Considerations

1. **Cache Optimization**: Implement longer cache duration for stable dokter data
2. **Endpoint Discovery**: Auto-detect optimal dokter endpoint format
3. **Rate Limiting**: Implement intelligent rate limiting for retry attempts
4. **Health Checks**: Regular validation of dokter endpoint availability

## Related Files

- `app/Traits/PcareTrait.php` - Main implementation
- `app/Http/Controllers/ReferensiDokterController.php` - Controller usage
- `config/bpjs.php` - Configuration
- `storage/logs/laravel.log` - Debug logs

## References

- BPJS PCare API Documentation
- Laravel HTTP Client Documentation
- PCare Integration Guidelines