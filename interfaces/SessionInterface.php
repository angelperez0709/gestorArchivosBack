<?php
declare(strict_types=1);
interface SessionInterface
{

    /**
     * Sets a session value.
     *
     * @param string $key   The key of the session value.
     * @param mixed  $value The value to be set.
     *
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * Gets a session value by key.
     *
     * @param string $key     The key of the session value.
     *
     * @return mixed The session value if found, otherwise the default value.
     */
    public function get(string $key) : mixed;

    /**
     * Removes a session value by key.
     *
     * @param string $key The key of the session value to remove.
     *
     * @return void
     */
    public function remove(string $key): void;

    /**
     * Clear the session.
     *
     * @return void
     */
    public function clear(): void;
}
