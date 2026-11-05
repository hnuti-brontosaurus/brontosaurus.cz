# Nastavit aliasy bash pro příkazy Gitu.
echo "Configuring bash aliases..."
cat >> ~/.bashrc << 'ALIASES_EOF'

# Aliasy Gitu
alias ggs='git status'
alias ggd='git diff'
alias ggds='git diff --staged'
alias ggb='git branch'
alias ggl='git log'
ALIASES_EOF

source ~/.bashrc
echo "Bash aliases configured successfully."