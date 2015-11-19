# SimpelNLParser
Parser for the network provider Simpel (NL)

## Usage

Place this PHP file on a webserver and query the php with the parameters ```username``` and ```password```. The result will look like the JSON below.

```javascript
{
    "bellen": {
        "current": 0,
        "max": 46,
        "type": "minuten"
    },
    "sms": {
        "current": 0,
        "max": 4667,
        "type": "SMS"
    },
    "internet": {
        "current": 3,
        "max": 466,
        "type": "MB"
    }
}
```
