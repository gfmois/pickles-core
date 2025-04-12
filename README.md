# Configure githooks
## Launch Scrip
```bash
chmod -x setup-git.sh
./setup-git.sh
```

## Or configure it manually

### Add permissions to pre-commit hook
```bash
chmod +x .githooks/pre-commit
```

### Configure git to use .githooks as hook directory
```bash
git config core.hooksPath .githooks
```


