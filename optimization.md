# Optimization (load times and rendering performance)

## Without Cache
The Excel process runs pretty fast even with the filters and with the file servers_filters_assignment.xlsx
we had response time of 225ms average o retrieve the server data list.

To test the file process load it was added a file with 3403 rows servers_filters_assignment_large.xlsx .
On this case response time was 1 minute and 34 seconds average to retrieve the server data list .

## With Cache
Enabling the cache with servers_filters_assignment.xlsx or servers_filters_assignment_large.xlsx the average response time was between 23ms and 44ms.

## Performance Test report

Refered to [here](Leaseweb-Rest-API-performance-report.pdf)

