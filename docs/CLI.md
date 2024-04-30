## SOLANA SDK CLI for Newbies

[Solana CLI Installation Instructions](https://docs.solanalabs.com/cli/install)

## Why installing the CLI?

I actually installed the CLI long time ago but never used it and tried with no luck to get things working using Vanilla JS and the Web3.js in intermittent attempts. ( hint: It doesn't work because the Web3.js package uses node.js libraries, as far as I know ) 

So, why is it needed? 

Answer: Because the browser wallets provide very limited functionality when using them in DEV mode. 

## Getting Started

#### 1- **Install a FileSystem Wallet (DEVNET)** [Docs](https://docs.solanalabs.com/cli/wallets/paper)

DO NOT do this for a Mainnet wallet as your keys would be residing in the Filesystem. You need to provide a valid URL, custom RPCs are not supported ( not recognized )

```bash
 solana config set --url https://api.devnet.solana.com
```

#### 2- Fund the Wallet with more than SOL: 

The following cmd generates a new keypair and stores it in the following path by default: _/Users/[yourUser]/.config/solana/id.json_

```bash
solana-keygen new
```

#### 3- Verifying the configuration: 

```bash
solana config get
```

Output: 

```bash 
Config File: /Users/eduardochongkan/.config/solana/cli/config.yml
RPC URL: https://api.devnet.solana.com
WebSocket URL: wss://api.devnet.solana.com/ (computed)
Keypair Path: Pasv78UQDTJyPLSS8FTtCekyZHJoPcZCjiGWkr7hdzy.json
Commitment: confirmed
```

#### 4- Funding the Wallet:

To fund the local wallet: 
```bash
solana airdrop 1
```

To fund a remote wallet, e.g. a Phantom Wallet. If it fails due to Rate Limiting, try again with 0.5 Sol. 

```bash
solana airdrop 1 3Js7k6xYQbvXv6qUYLapYV7Sptfg37Tss9GcAyVEuUqk 
```
OR 
```bash
solana airdrop 0.5 3Js7k6xYQbvXv6qUYLapYV7Sptfg37Tss9GcAyVEuUqk 
```

Then 

```bash
solana balance
```
OR 
```bash
solana balance 3Js7k6xYQbvXv6qUYLapYV7Sptfg37Tss9GcAyVEuUqk 
```

#### Wrapped Sol (wSol)

```bash
spl-token --url devnet wrap 0.5 
```
Outputs:
```bash
Wrapping 0.5 SOL into GdDubLim7hPPv95X2xPdKtbvWt4gJJ4uHfUYrPbxmKuZ

Signature: ertqCkWCkwtgCzDFtXbyNvFL9zEiqTmHX6zjMxpGXqAuPH8n7FCEaAnL7MiQpmL6cWGVWtEBVPXcRkqSKAvYNrx
```

#### Getting Token Balance

```bash
solana balance GdDubLim7hPPv95X2xPdKtbvWt4gJJ4uHfUYrPbxmKuZ
```
Outputs (that is wrapped Sol (wSol))
```bash
0.5 SOL
```
OR

```bash
spl-token 
```

### Some DEVNET Token Mints: 

- [Devnet USDC](https://explorer.solana.com/address/DL4ivZm3NVHWk9ZvtcqTchxoKArDK4rT3vbDx2gYVr7P?cluster=devnet): 4zMMC9srt5Ri5X14GAgXhaHii3GnPAEERYPJgZJDncDU
- [Devnet USDT](https://explorer.solana.com/address/EJwZgeZrdC8TXTQbQBoL6bfuAnFUUy1PVCMB4DYPzVaS?cluster=devnet): EJwZgeZrdC8TXTQbQBoL6bfuAnFUUy1PVCMB4DYPzVaS
- [Devnet SOL (wSOL](https://explorer.solana.com/address/So11111111111111111111111111111111111111112?cluster=devnet): So11111111111111111111111111111111111111112
