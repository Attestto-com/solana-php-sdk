# Connection Class

The `Connection` class is part of the `Attestto\SolanaPhpSdk` namespace and extends the `Program` class. It provides methods to interact with the Solana network.

## Methods

### `getAccountInfo(string $pubKey): array`

This method retrieves the account information for a given public key. It throws an `AccountNotFoundException` if the account is not found.

### `getBalance(string $pubKey): float`

This method retrieves the balance for a given public key.

### `getConfirmedTransaction(string $transactionSignature): array|null`

This method retrieves a confirmed transaction using the transaction signature.

### `getTransaction(string $transactionSignature): array|null`

This method retrieves a transaction using the transaction signature. This method is only available in solana-core v1.7 or newer.

### `getLatestBlockhash(?Commitment $commitment): array`

This method retrieves the latest blockhash.

### `sendTransaction(Transaction $transaction, array $signers, array $params = []): array|Response`

This method sends a transaction. It signs the transaction with the provided signers and serializes it to a binary string.

### `simulateTransaction(Transaction $transaction, array $signers, array $params = []): array|Response`

This method simulates a transaction. It signs the transaction with the provided signers and serializes it to a binary string.

