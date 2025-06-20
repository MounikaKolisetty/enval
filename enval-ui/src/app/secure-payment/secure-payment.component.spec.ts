import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SecurePaymentComponent } from './secure-payment.component';

describe('SecurePaymentComponent', () => {
  let component: SecurePaymentComponent;
  let fixture: ComponentFixture<SecurePaymentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SecurePaymentComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SecurePaymentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
