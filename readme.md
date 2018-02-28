# About
---

PHP Data Cacher is a plugin that caches json data returned from a REST query. 

We created this because we needed a way to grab json data from an external api and load it quickly in the browser for users to see. The data wasn't changing often so the quickest way to show it would be to pull the data from a local file. The plugin provides options for:

1. How long to wait until a local json file is refreshed.
2. How many users can view the data before it needs to be refreshed.