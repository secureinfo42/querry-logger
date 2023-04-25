# Query logger

## To send data

```sh
# Send get
curl -X GET "https://localhost/logger/?variable1=value1&var2=val2&..."

# Send post
curl -X POST -d "post_variable1=value1&post_variable2=value2" "https://localhost/logger/"

# Upload file
curl -ks# -d @./path/to/file "https://localhost/logger/"
```

## To inspect data

```
https://localhost/logger/?inspect
```


## To clear data

```
https://localhost/logger/?clear
```


