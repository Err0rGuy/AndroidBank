package com.example.swiftpay; // Make sure this matches your app's package name

import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.util.Log;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.example.swiftpay.logic.API.APIService;
import com.google.android.material.card.MaterialCardView;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.Arrays;
import java.util.concurrent.Executors;

public class DashboardActivity extends AppCompatActivity {

    private TextView tvCurrentMoneyValue;
    private TextView tvUserName;
    private TextView tvUserEmail;
    private TextView tvAccountNumber;
    private TextView tvAccountType;
    private TextView tvLastTransaction;
    private ImageView ivUserProfile;
    private MaterialCardView cardNewTransaction;
    private MaterialCardView cardTransactionHistory;
    private MaterialCardView cardCurrencyMoney;
    private Button btnLogout;

    @SuppressLint("MissingInflatedId")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dashboard);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        tvCurrentMoneyValue = findViewById(R.id.tvCurrentMoneyValue);
        tvUserName = findViewById(R.id.tvUserName);
        tvAccountNumber = findViewById(R.id.tvAccountNumber);
        tvAccountType = findViewById(R.id.tvAccountType);
        ivUserProfile = findViewById(R.id.ivUserProfile);
        cardNewTransaction = findViewById(R.id.cardNewTransaction);
        cardTransactionHistory = findViewById(R.id.cardTransactionHistory);
        btnLogout = findViewById(R.id.btnLogout);
        tvUserEmail = findViewById(R.id.tvUserEmail);
        cardCurrencyMoney = findViewById(R.id.cardCurrentMoney);
        tvLastTransaction = findViewById(R.id.tvLastTransaction);

        loadUserData();

        cardNewTransaction.setOnClickListener(v -> {
            Intent intent = new Intent(DashboardActivity.this, NewTransactionActivity.class);
            startActivity(intent);
            Toast.makeText(DashboardActivity.this, "Opening New Transaction...", Toast.LENGTH_SHORT).show();
        });

        cardTransactionHistory.setOnClickListener(v -> {
            Intent intent = new Intent(DashboardActivity.this, TransactionHistoryActivity.class);
            startActivity(intent);
            Toast.makeText(DashboardActivity.this, "Opening Transaction History...", Toast.LENGTH_SHORT).show();
        });

        btnLogout.setOnClickListener(v -> {
            Toast.makeText(DashboardActivity.this, "Logging out...", Toast.LENGTH_LONG).show();
            logout();
        });

        cardCurrencyMoney.setOnClickListener(v -> {
            if (Double.parseDouble(tvCurrentMoneyValue.getText().toString().replace("$", "").trim()) > 50_000)
                Toast.makeText(DashboardActivity.this, "Rich Guy", Toast.LENGTH_LONG).show();
            else
                Toast.makeText(DashboardActivity.this, "Poor Guy", Toast.LENGTH_LONG).show();
        });

        ivUserProfile.setOnClickListener(v -> Toast.makeText(DashboardActivity.this, "So what?", Toast.LENGTH_SHORT).show());
    }

    private void logout(){
        var executor = Executors.newSingleThreadExecutor();
        executor.execute(() -> {
            var loggedOut = APIService.logout();
            if (loggedOut) {
                new Handler(Looper.getMainLooper()).post(() -> {
                    Intent intent = new Intent(this, LoginActivity.class);
                    startActivity(intent);
                    finish();
                });
            }
        });
    }
    @SuppressLint("SetTextI18n")
    private void loadUserData() {

        var executor = Executors.newSingleThreadExecutor();
        executor.execute(() -> {
            Handler handler = new Handler(Looper.getMainLooper());
            var userData = APIService.fetchData();
            if (userData != null) {
                handler.post(() -> {
                    String fullName;
                    String userEmail;
                    String balance;
                    String accountNumber;
                    String lastTransaction;
                    Log.d("Detaillllllllllllllllllllllllllll --> ", String.valueOf(userData));
                    try {
                        JSONObject user = userData.getJSONObject("user");
                        JSONObject account = userData.getJSONObject("account");
                        fullName = user.getString("first_name") + " " + user.getString("last_name");
                        userEmail = user.getString("email");
                        balance = account.getString("balance");
                        accountNumber = account.getString("account_number");
                        lastTransaction = account.getString("last_transaction_at");
                    } catch (JSONException e) {
                        Log.d("Exception happened --> ", Arrays.toString(e.getStackTrace()));
                        throw new RuntimeException(e);
                    }
                    tvUserName.setText(fullName);
                    tvUserEmail.setText(userEmail);
                    tvCurrentMoneyValue.setText("$" + balance);
                    tvAccountNumber.setText(accountNumber);
                    tvAccountType.setText("Saving Account");
                    if (lastTransaction.equals("null"))
                        tvLastTransaction.setText("no transaction yet.");
                    else
                        tvLastTransaction.setText(lastTransaction);
                });
            }

        });

    }

    @Override
    protected void onResume() {
        super.onResume();
        loadUserData();
    }
}