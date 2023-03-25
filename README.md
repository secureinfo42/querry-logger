# Query logger

## To send data

```sh
# Send get
curl -X GET "https://logger.secureinfo.eu/?variable1=value1&var2=val2&..."

# Send post
curl -X POST -d "post_variable1=value1&post_variable2=value2" "https://logger.secureinfo.eu/"

# Upload file
curl -ks# -d @./path/to/file "https://logger.secureinfo.eu/"
```

## To inspect data

```
https://logger.secureinfo.eu/?inspect
```


## To clear data

```
https://logger.secureinfo.eu/?clear
```


